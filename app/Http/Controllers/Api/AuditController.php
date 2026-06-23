<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\DressItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    /**
     * Scan a QR code and save audit record
     */
    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        // Find the dress item by barcode
        $dressItem = DressItem::with(['dress.collection'])
            ->where('barcode', $request->barcode)
            ->first();

        if (!$dressItem) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode not found in inventory',
            ], 404);
        }

        // Check if this item was already scanned today by this user
        $existingAudit = Audit::where('dress_item_id', $dressItem->id)
            ->where('scanned_by', Auth::id())
            ->whereDate('scan_date', today())
            ->first();

        if ($existingAudit) {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate scan! This item was already scanned today.',
                'data' => [
                    'existing_audit' => $existingAudit,
                    'scanned_at' => $existingAudit->scan_date->format('h:i A'),
                ],
            ], 409);
        }

        // Create audit record with collection name, dress name, size, and status
        $audit = Audit::create([
            'dress_item_id' => $dressItem->id,
            'barcode' => $request->barcode,
            'collection_name' => $dressItem->dress->collection->name,
            'dress_name' => $dressItem->dress->name,
            'size' => $dressItem->dress->size,
            'status' => $dressItem->status,
            'scanned_by' => Auth::id(),
            'scan_date' => now(),
        ]);

        // Load relationships for response
        $audit->load(['dressItem.dress.collection', 'scannedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Item scanned successfully',
            'data' => [
                'audit' => $audit,
                'dress_item' => $dressItem,
                'dress' => $dressItem->dress,
                'collection' => $dressItem->dress->collection,
            ],
        ], 201);
    }

    /**
     * Get all audit records with filters
     */
    public function index(Request $request)
    {
        $query = Audit::with(['dressItem.dress.collection', 'scannedBy']);

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('scan_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('scan_date', '<=', $request->end_date);
        }

        // Filter by user
        if ($request->has('scanned_by')) {
            $query->where('scanned_by', $request->scanned_by);
        }

        // Filter by barcode
        if ($request->has('barcode')) {
            $query->where('barcode', 'like', '%' . $request->barcode . '%');
        }

        // Sort by most recent first
        $query->orderBy('scan_date', 'desc');

        // Paginate results
        $perPage = $request->get('per_page', 50);
        $audits = $query->paginate($perPage);

        return response()->json($audits);
    }

    /**
     * Get audit statistics
     */
    public function stats(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfDay());
        $endDate = $request->get('end_date', now()->endOfDay());

        $totalScans = Audit::whereBetween('scan_date', [$startDate, $endDate])->count();
        $uniqueItems = Audit::whereBetween('scan_date', [$startDate, $endDate])
            ->distinct('dress_item_id')
            ->count('dress_item_id');
        
        $todayScans = Audit::whereDate('scan_date', today())->count();
        
        $topScanner = Audit::whereBetween('scan_date', [$startDate, $endDate])
            ->selectRaw('scanned_by, COUNT(*) as scan_count')
            ->groupBy('scanned_by')
            ->orderBy('scan_count', 'desc')
            ->with('scannedBy')
            ->first();

        return response()->json([
            'total_scans' => $totalScans,
            'unique_items' => $uniqueItems,
            'today_scans' => $todayScans,
            'top_scanner' => $topScanner,
        ]);
    }

    /**
     * Get recent scans
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);

        $audits = Audit::with(['dressItem.dress.collection', 'scannedBy'])
            ->orderBy('scan_date', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($audits);
    }

    /**
     * Delete an audit record
     */
    public function destroy(Audit $audit)
    {
        $audit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Audit record deleted successfully',
        ]);
    }

    /**
     * Export audit records to CSV
     */
    public function export(Request $request)
    {
        $query = Audit::with(['scannedBy']);

        // Apply filters
        if ($request->has('start_date')) {
            $query->where('scan_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('scan_date', '<=', $request->end_date);
        }

        if ($request->has('scanned_by')) {
            $query->where('scanned_by', $request->scanned_by);
        }

        $audits = $query->orderBy('scan_date', 'desc')->get();

        // Create CSV content
        $csvData = "ID,Barcode,Collection,Dress,Size,Status,Scanned By,Scan Date\n";

        foreach ($audits as $audit) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s\n",
                $audit->id,
                $audit->barcode,
                $audit->collection_name,
                $audit->dress_name,
                $audit->size,
                $audit->status,
                $audit->scannedBy ? $audit->scannedBy->name : 'Unknown',
                $audit->scan_date->format('Y-m-d H:i:s')
            );
        }

        $filename = 'audit_export_' . now()->format('Y-m-d_His') . '.csv';

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

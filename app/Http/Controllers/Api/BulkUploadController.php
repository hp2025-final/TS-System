<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Dress;
use App\Models\DressItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BulkUploadController extends Controller
{
    /**
     * Process CSV file upload
     */
    public function upload(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file. Please upload a CSV file.',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        // Read CSV file
        $csvData = array_map('str_getcsv', file($path));
        
        // Remove header row
        $header = array_shift($csvData);
        
        // Validate header structure
        $expectedHeaders = [
            'Collection_Name',
            'Dress_Name',
            'SKU',
            'Dress_Item_Barcode',
            'Cost_Price',
            'Sale_Price',
            'Collection_Discount_%',
            'Dress_Discount_%',
            'Dress_Item_Discount_%'
        ];
        
        if ($header !== $expectedHeaders) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid CSV structure. Expected columns: ' . implode(', ', $expectedHeaders),
                'received' => $header
            ], 422);
        }

        $results = [
            'total' => count($csvData),
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => [],
            'existing' => [],
            'created' => [
                'collections' => 0,
                'dresses' => 0,
                'dress_items' => 0
            ]
        ];

        DB::beginTransaction();

        try {
            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed
                
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Parse row data
                    $collectionName = trim($row[0]);
                    $dressName = trim($row[1]);
                    $sku = trim($row[2]);
                    $barcode = trim($row[3]);
                    $costPrice = floatval($row[4]);
                    $salePrice = floatval($row[5]);
                    $collectionDiscount = floatval($row[6] ?? 0);
                    $dressDiscount = floatval($row[7] ?? 0);
                    $dressItemDiscount = floatval($row[8] ?? 0);

                    // Validate required fields
                    if (empty($collectionName) || empty($dressName) || empty($sku) || empty($barcode)) {
                        $results['errors'][] = "Row {$rowNumber}: Missing required fields (Collection_Name, Dress_Name, SKU, or Barcode)";
                        $results['failed']++;
                        continue;
                    }

                    // Check if barcode already exists
                    $existingItem = DressItem::where('barcode', $barcode)->first();
                    if ($existingItem) {
                        $results['existing'][] = [
                            'row' => $rowNumber,
                            'barcode' => $barcode,
                            'collection' => $collectionName,
                            'dress' => $dressName,
                            'sku' => $sku,
                            'message' => 'Barcode already exists in database'
                        ];
                        $results['skipped']++;
                        continue;
                    }

                    // Find or create Collection
                    $collection = Collection::firstOrCreate(
                        ['name' => $collectionName],
                        [
                            'description' => null,
                            'discount_percentage' => $collectionDiscount,
                            'discount_active' => $collectionDiscount > 0 ? 1 : 0,
                            'status' => 'active'
                        ]
                    );

                    if ($collection->wasRecentlyCreated) {
                        $results['created']['collections']++;
                    } else {
                        // Update discount if provided and different
                        if ($collectionDiscount > 0 && $collection->discount_percentage != $collectionDiscount) {
                            $collection->update([
                                'discount_percentage' => $collectionDiscount,
                                'discount_active' => 1
                            ]);
                        }
                    }

                    // Find or create Dress
                    $dress = Dress::firstOrCreate(
                        [
                            'collection_id' => $collection->id,
                            'sku' => $sku
                        ],
                        [
                            'name' => $dressName,
                            'description' => null,
                            'size' => 'unstitched',
                            'hs_code' => null,
                            'cost_price' => $costPrice,
                            'sale_price' => $salePrice,
                            'discount_percentage' => $dressDiscount,
                            'discount_active' => $dressDiscount > 0 ? 1 : 0,
                            'tax_percentage' => 0,
                            'status' => 'active'
                        ]
                    );

                    if ($dress->wasRecentlyCreated) {
                        $results['created']['dresses']++;
                    } else {
                        // Update prices and discount if provided
                        $updateData = [];
                        if ($costPrice > 0 && $dress->cost_price != $costPrice) {
                            $updateData['cost_price'] = $costPrice;
                        }
                        if ($salePrice > 0 && $dress->sale_price != $salePrice) {
                            $updateData['sale_price'] = $salePrice;
                        }
                        if ($dressDiscount > 0 && $dress->discount_percentage != $dressDiscount) {
                            $updateData['discount_percentage'] = $dressDiscount;
                            $updateData['discount_active'] = 1;
                        }
                        
                        if (!empty($updateData)) {
                            $dress->update($updateData);
                        }
                    }

                    // Create Dress Item
                    $dressItem = DressItem::create([
                        'dress_id' => $dress->id,
                        'barcode' => $barcode,
                        'size_discount_percentage' => $dressItemDiscount,
                        'size_discount_active' => $dressItemDiscount > 0 ? 1 : 0,
                        'status' => 'available'
                    ]);

                    $results['created']['dress_items']++;
                    $results['success']++;

                } catch (\Exception $e) {
                    $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                    $results['failed']++;
                }
            }

            DB::commit();

            $message = "Upload completed successfully! ";
            $message .= "{$results['success']} items created";
            if ($results['skipped'] > 0) {
                $message .= ", {$results['skipped']} skipped (already exist)";
            }
            if ($results['failed'] > 0) {
                $message .= ", {$results['failed']} failed";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
                'results' => $results
            ], 500);
        }
    }

    /**
     * Get upload statistics
     */
    public function stats()
    {
        return response()->json([
            'total_collections' => Collection::count(),
            'total_dresses' => Dress::count(),
            'total_dress_items' => DressItem::count(),
            'available_items' => DressItem::whereIn('status', ['available', 'returned_resaleable'])->count(),
            'sold_items' => DressItem::where('status', 'sold')->count(),
        ]);
    }

    /**
     * Download sample CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Collection_Name',
            'Dress_Name',
            'SKU',
            'Dress_Item_Barcode',
            'Cost_Price',
            'Sale_Price',
            'Collection_Discount_%',
            'Dress_Discount_%',
            'Dress_Item_Discount_%'
        ];

        $sampleData = [
            ['Summer Collection', 'Floral Dress', 'SKU-FL001', 'BC001', '1500', '2500', '10', '5', '0'],
            ['Summer Collection', 'Floral Dress', 'SKU-FL001', 'BC002', '1500', '2500', '10', '5', '0'],
            ['Winter Collection', 'Wool Coat', 'SKU-WC001', 'BC003', '3000', '5000', '15', '0', '5'],
        ];

        $csvContent = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="bulk_upload_template.csv"');
    }

    /**
     * Bulk retrieve items
     */
    public function bulkRetrieve(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file. Please upload a CSV file.',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        // Read CSV file
        $csvData = array_map('str_getcsv', file($path));
        
        // Remove header row if exists
        $header = array_shift($csvData);
        
        // Validate header structure (should just be "Barcode")
        if ($header !== ['Barcode']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid CSV structure. Expected column: Barcode',
                'received' => $header
            ], 422);
        }

        $results = [
            'total' => count($csvData),
            'success' => 0,
            'not_found' => 0,
            'errors' => [],
            'not_found_barcodes' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed
                
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $barcode = trim($row[0]);

                    // Validate barcode
                    if (empty($barcode)) {
                        $results['errors'][] = "Row {$rowNumber}: Empty barcode";
                        continue;
                    }

                    // Find dress item by barcode
                    $dressItem = DressItem::where('barcode', $barcode)->first();

                    if (!$dressItem) {
                        $results['not_found_barcodes'][] = [
                            'row' => $rowNumber,
                            'barcode' => $barcode,
                            'message' => 'Barcode not found in database'
                        ];
                        $results['not_found']++;
                        continue;
                    }

                    // Update status to retrieved_ho
                    $dressItem->update([
                        'status' => 'retrieved_ho',
                        'updated_at' => now()
                    ]);

                    $results['success']++;

                } catch (\Exception $e) {
                    $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Retrieve completed successfully! ";
            $message .= "{$results['success']} items retrieved";
            if ($results['not_found'] > 0) {
                $message .= ", {$results['not_found']} barcodes not found";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Retrieve failed: ' . $e->getMessage(),
                'results' => $results
            ], 500);
        }
    }

    /**
     * Download retrieve template
     */
    public function downloadRetrieveTemplate()
    {
        $headers = ['Barcode'];

        $sampleData = [
            ['BC001'],
            ['BC002'],
            ['BC003'],
        ];

        $csvContent = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="bulk_retrieve_template.csv"');
    }
}

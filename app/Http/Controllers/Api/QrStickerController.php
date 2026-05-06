<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QrStickerController extends Controller
{
    /**
     * Process CSV file upload and return parsed data for QR sticker generation
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

        // Normalize headers (trim whitespace, handle BOM)
        $header = array_map(function ($h) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
        }, $header);

        // Validate header structure
        $expectedHeaders = [
            'Collection',
            'Dress',
            'SKU',
            'Size',
            'Sale_Price',
            'QR_Code_Number'
        ];

        // Case-insensitive header match
        $normalizedHeader = array_map('strtolower', $header);
        $normalizedExpected = array_map('strtolower', $expectedHeaders);

        if ($normalizedHeader !== $normalizedExpected) {
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
            'errors' => [],
            'items' => []
        ];

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because header removed and 0-indexed

            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Ensure row has enough columns
                if (count($row) < 6) {
                    $results['errors'][] = "Row {$rowNumber}: Missing columns. Expected 6 columns.";
                    $results['failed']++;
                    continue;
                }

                $collection = trim($row[0]);
                $dress = trim($row[1]);
                $sku = trim($row[2]);
                $size = trim($row[3]);
                $salePrice = trim($row[4]);
                $qrCodeNumber = trim($row[5]);

                // Validate required fields
                if (empty($collection) || empty($dress) || empty($sku) || empty($qrCodeNumber)) {
                    $results['errors'][] = "Row {$rowNumber}: Missing required fields (Collection, Dress, SKU, or QR_Code_Number)";
                    $results['failed']++;
                    continue;
                }

                $results['items'][] = [
                    'row' => $rowNumber,
                    'collection' => $collection,
                    'dress' => $dress,
                    'sku' => $sku,
                    'size' => $size ?: 'N/A',
                    'sale_price' => $salePrice ?: '',
                    'qr_code_number' => $qrCodeNumber,
                ];

                $results['success']++;

            } catch (\Exception $e) {
                $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                $results['failed']++;
            }
        }

        $message = "CSV parsed successfully! {$results['success']} items ready for sticker generation.";
        if ($results['failed'] > 0) {
            $message .= " {$results['failed']} rows had errors.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'results' => $results
        ]);
    }

    /**
     * Download sample CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Collection',
            'Dress',
            'SKU',
            'Size',
            'Sale_Price',
            'QR_Code_Number'
        ];

        $sampleData = [
            ['Summer Collection', 'Floral Dress', 'SKU-FL001', 'M', '2500', '2503071'],
            ['Summer Collection', 'Floral Dress', 'SKU-FL001', 'L', '2500', '2503072'],
            ['Winter Collection', 'Wool Coat', 'SKU-WC001', 'XL', '5000', '2503085'],
            ['Winter Collection', 'Wool Coat', 'SKU-WC001', 'S', '5000', '2503086'],
        ];

        $csvContent = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="qr_sticker_template.csv"');
    }
}

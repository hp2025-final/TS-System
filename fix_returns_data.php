<?php
// Fix duplicate returns and update dress item statuses

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

echo "=== Fixing Duplicate Returns and Dress Item Statuses ===\n\n";

try {
    DB::beginTransaction();
    
    // 1. Find and remove duplicate returns (keep the latest one for each sale_item/dress_item combination)
    echo "1. Checking for duplicate returns...\n";
    
    $duplicateReturns = DB::select("
        SELECT sale_item_id, dress_item_id, COUNT(*) as count, MIN(id) as keep_id, GROUP_CONCAT(id) as all_ids
        FROM returns 
        GROUP BY sale_item_id, dress_item_id 
        HAVING COUNT(*) > 1
    ");
    
    if (!empty($duplicateReturns)) {
        foreach ($duplicateReturns as $duplicate) {
            $allIds = explode(',', $duplicate->all_ids);
            $keepId = $duplicate->keep_id;
            $deleteIds = array_diff($allIds, [$keepId]);
            
            echo "  Found {$duplicate->count} returns for sale_item_id: {$duplicate->sale_item_id}, dress_item_id: {$duplicate->dress_item_id}\n";
            echo "  Keeping return ID: {$keepId}, deleting IDs: " . implode(', ', $deleteIds) . "\n";
            
            // Delete duplicate returns
            DB::table('returns')->whereIn('id', $deleteIds)->delete();
        }
        echo "  Removed duplicate returns.\n\n";
    } else {
        echo "  No duplicate returns found.\n\n";
    }
    
    // 2. Update dress item statuses for returned items
    echo "2. Updating dress item statuses...\n";
    
    $returnedItems = DB::table('returns')
        ->join('dress_items', 'returns.dress_item_id', '=', 'dress_items.id')
        ->select('dress_items.id', 'dress_items.barcode', 'dress_items.status')
        ->get();
    
    foreach ($returnedItems as $item) {
        if ($item->status !== 'returned') {
            echo "  Updating dress item ID: {$item->id}, Barcode: {$item->barcode} from '{$item->status}' to 'returned'\n";
            DB::table('dress_items')
                ->where('id', $item->id)
                ->update([
                    'status' => 'returned',
                    'returned_at' => now(),
                    'updated_at' => now()
                ]);
        }
    }
    
    // 3. Check for any sold items that should not be sold (have returns but are still marked as sold)
    echo "\n3. Checking for incorrectly marked sold items...\n";
    
    $incorrectSold = DB::table('dress_items')
        ->join('returns', 'dress_items.id', '=', 'returns.dress_item_id')
        ->where('dress_items.status', 'sold')
        ->select('dress_items.id', 'dress_items.barcode')
        ->get();
    
    if (!empty($incorrectSold)) {
        foreach ($incorrectSold as $item) {
            echo "  Fixing dress item ID: {$item->id}, Barcode: {$item->barcode} - should be 'returned' not 'sold'\n";
            DB::table('dress_items')
                ->where('id', $item->id)
                ->update([
                    'status' => 'returned',
                    'returned_at' => now(),
                    'updated_at' => now()
                ]);
        }
    } else {
        echo "  No incorrectly marked sold items found.\n";
    }
    
    DB::commit();
    
    echo "\n=== Data Fix Complete ===\n";
    echo "Summary:\n";
    echo "- Removed duplicate returns\n";
    echo "- Updated dress item statuses to 'returned' for all returned items\n";
    echo "- Fixed any incorrectly marked sold items\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}

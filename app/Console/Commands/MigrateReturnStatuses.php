<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReturnItem;
use App\Models\DressItem;
use Illuminate\Support\Facades\DB;

class MigrateReturnStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'returns:migrate-statuses {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing return data to use new return reason-based status classification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->info('🚀 Starting return status migration...');
        }

        // Define return reason classifications
        $resaleableReasons = [
            'wrong_size',
            'customer_request',
            'changed_mind',
            'style_preference'
        ];

        $defectiveReasons = [
            'defective',
            'damaged',
            'quality_issue',
            'torn',
            'stained',
            'broken'
        ];

        // Get all returns
        $returns = ReturnItem::with('dressItem')->get();
        
        if ($returns->isEmpty()) {
            $this->info('📋 No returns found in the database.');
            return;
        }

        $this->info("📊 Found {$returns->count()} returns to process");
        
        $resaleableCount = 0;
        $defectiveCount = 0;
        $unknownCount = 0;
        $processedItems = collect();

        foreach ($returns as $return) {
            if (!$return->dressItem) {
                $this->warn("⚠️  Return ID {$return->id}: No associated dress item found");
                continue;
            }

            $currentStatus = $return->dressItem->status;
            $returnReason = strtolower($return->return_reason);
            $newStatus = null;

            // Determine new status based on return reason
            if (in_array($returnReason, $resaleableReasons)) {
                $newStatus = 'returned_resaleable';
                $resaleableCount++;
            } elseif (in_array($returnReason, $defectiveReasons)) {
                $newStatus = 'returned_defective';
                $defectiveCount++;
            } else {
                // Default unknown reasons to resaleable for manual review
                $newStatus = 'returned_resaleable';
                $unknownCount++;
                $this->warn("⚠️  Unknown return reason '{$return->return_reason}' for Return ID {$return->id} - defaulting to resaleable");
            }

            $processedItems->push([
                'return_id' => $return->id,
                'dress_item_id' => $return->dressItem->id,
                'barcode' => $return->dressItem->barcode,
                'current_status' => $currentStatus,
                'new_status' => $newStatus,
                'return_reason' => $return->return_reason,
                'return_date' => $return->return_date
            ]);
        }

        // Display summary
        $this->table([
            'Status', 'Count', 'Description'
        ], [
            ['Resaleable', $resaleableCount, 'Items that can be quality-checked and resold'],
            ['Defective', $defectiveCount, 'Items that cannot be resold'],
            ['Unknown Reasons', $unknownCount, 'Items with unknown reasons (defaulted to resaleable)']
        ]);

        if ($dryRun) {
            $this->info('📋 Items that would be updated:');
            foreach ($processedItems->take(10) as $item) {
                $this->line("  • Return #{$item['return_id']} | {$item['barcode']} | {$item['current_status']} → {$item['new_status']} | Reason: {$item['return_reason']}");
            }
            
            if ($processedItems->count() > 10) {
                $this->info("  ... and " . ($processedItems->count() - 10) . " more items");
            }
            
            $this->info("\n💡 Run without --dry-run flag to apply these changes");
            return;
        }

        // Confirm before proceeding
        if (!$dryRun && !$this->confirm('Do you want to proceed with updating these items?', true)) {
            $this->info('❌ Migration cancelled');
            return;
        }

        // Start transaction
        DB::beginTransaction();
        
        try {
            $updated = 0;
            
            foreach ($processedItems as $item) {
                $dressItem = DressItem::find($item['dress_item_id']);
                if ($dressItem && in_array($dressItem->status, ['returned', 'returned_resaleable'])) {
                    $dressItem->status = $item['new_status'];
                    $dressItem->save();
                    $updated++;
                }
            }

            DB::commit();
            
            $this->info("✅ Successfully updated {$updated} items");
            $this->info("📊 Summary:");
            $this->info("   • {$resaleableCount} items marked as resaleable");
            $this->info("   • {$defectiveCount} items marked as defective");
            if ($unknownCount > 0) {
                $this->warn("   • {$unknownCount} items had unknown return reasons (review recommended)");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Migration failed: " . $e->getMessage());
            return 1;
        }

        $this->info("\n🎉 Return status migration completed successfully!");
        $this->info("💡 You can now use the inventory management interface to handle resaleable items");
        
        return 0;
    }
}

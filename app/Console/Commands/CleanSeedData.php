<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Collection;
use App\Models\Dress;
use App\Models\DressItem;
use App\Models\Sale;

class CleanSeedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-seed-data {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all seed data from database except users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will remove all collections, dresses, dress items, and sales data. Are you sure?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('Starting cleanup of seed data...');

        try {
            // Show current counts
            $this->showCurrentCounts();

            // Delete data in order to respect foreign key constraints
            $this->info('Deleting sales data...');
            DB::table('sale_items')->delete();
            DB::table('sales')->delete();

            $this->info('Deleting dress items...');
            DB::table('dress_items')->delete();

            $this->info('Deleting dresses...');
            DB::table('dresses')->delete();

            $this->info('Deleting collections...');
            DB::table('collections')->delete();

            // Reset auto-increment values
            $this->info('Resetting auto-increment values...');
            DB::statement('ALTER TABLE collections AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE dresses AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE dress_items AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE sales AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE sale_items AUTO_INCREMENT = 1');

            $this->info('Cleanup completed successfully!');
            $this->showCurrentCounts();

        } catch (\Exception $e) {
            $this->error('Error during cleanup: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Show current data counts
     */
    private function showCurrentCounts()
    {
        $this->table(
            ['Table', 'Count'],
            [
                ['Users', User::count()],
                ['Collections', Collection::count()],
                ['Dresses', Dress::count()],
                ['Dress Items', DressItem::count()],
                ['Sales', Sale::count()],
            ]
        );
    }
}

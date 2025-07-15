<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

// Load environment configuration
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Starting database cleanup and reseeding process...\n";

    // Disable foreign key checks temporarily
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Clear tables in the correct order (respecting foreign key dependencies)
    $tables = ['returns', 'sale_items', 'sales', 'dress_items', 'dresses', 'collections'];
    
    foreach ($tables as $table) {
        DB::table($table)->truncate();
        echo "✓ Cleared $table table\n";
    }

    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    echo "✓ All specified tables cleared successfully (user data preserved)\n";
    echo "Starting seeding process...\n";

    // Run the seeder
    $exitCode = 0;
    system('php artisan db:seed --class=CollectionSeeder', $exitCode);
    if ($exitCode !== 0) {
        throw new Exception("Collection seeding failed");
    }
    
    system('php artisan db:seed --class=DressSeeder', $exitCode);
    if ($exitCode !== 0) {
        throw new Exception("Dress seeding failed");
    }
    
    system('php artisan db:seed --class=DressItemSeeder', $exitCode);
    if ($exitCode !== 0) {
        throw new Exception("Dress item seeding failed");
    }

    echo "✓ Database cleanup and reseeding completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

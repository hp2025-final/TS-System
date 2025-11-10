<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add a temporary column
        Schema::table('dress_items', function (Blueprint $table) {
            $table->string('temp_status')->nullable();
        });
        
        // Step 2: Copy current status values to temp column
        DB::statement("UPDATE dress_items SET temp_status = status");
        
        // Step 3: Drop the old status column
        Schema::table('dress_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Step 4: Create new status column with updated enum (added retrieved_ho)
        Schema::table('dress_items', function (Blueprint $table) {
            $table->enum('status', ['available', 'sold', 'returned_defective', 'returned_resaleable', 'damaged', 'retrieved_ho'])
                  ->default('available')
                  ->after('barcode');
        });
        
        // Step 5: Restore old values
        DB::statement("UPDATE dress_items SET status = temp_status");
        
        // Step 6: Drop the temporary column
        Schema::table('dress_items', function (Blueprint $table) {
            $table->dropColumn('temp_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add a temporary column
        Schema::table('dress_items', function (Blueprint $table) {
            $table->string('temp_status')->nullable();
        });
        
        // Step 2: Copy current status values to temp column, mapping retrieved_ho back to available
        DB::statement("UPDATE dress_items SET temp_status = CASE 
            WHEN status = 'retrieved_ho' THEN 'available'
            ELSE status
        END");
        
        // Step 3: Drop the new status column
        Schema::table('dress_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Step 4: Create old status column without retrieved_ho
        Schema::table('dress_items', function (Blueprint $table) {
            $table->enum('status', ['available', 'sold', 'returned_defective', 'returned_resaleable', 'damaged'])
                  ->default('available')
                  ->after('barcode');
        });
        
        // Step 5: Restore old values
        DB::statement("UPDATE dress_items SET status = temp_status");
        
        // Step 6: Drop the temporary column
        Schema::table('dress_items', function (Blueprint $table) {
            $table->dropColumn('temp_status');
        });
    }
};

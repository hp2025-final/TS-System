<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            // Add fields to link exchange to new sale records
            $table->foreignId('exchange_sale_id')->nullable()->constrained('sales')->onDelete('set null');
            $table->foreignId('exchange_sale_item_id')->nullable()->constrained('sale_items')->onDelete('set null');
        });
        
        Schema::table('sale_items', function (Blueprint $table) {
            // Add field to identify items that came from exchanges
            $table->foreignId('exchange_return_id')->nullable()->constrained('returns')->onDelete('set null');
            $table->boolean('is_exchange_item')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropForeign(['exchange_sale_id']);
            $table->dropForeign(['exchange_sale_item_id']);
            $table->dropColumn(['exchange_sale_id', 'exchange_sale_item_id']);
        });
        
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['exchange_return_id']);
            $table->dropColumn(['exchange_return_id', 'is_exchange_item']);
        });
    }
};

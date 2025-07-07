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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('dress_item_id')->constrained()->onDelete('cascade');
            $table->string('dress_name');
            $table->string('collection_name');
            $table->string('sku');
            $table->string('size');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('collection_discount_percentage', 5, 2)->default(0);
            $table->decimal('dress_discount_percentage', 5, 2)->default(0);
            $table->decimal('size_discount_percentage', 5, 2)->default(0);
            $table->decimal('total_discount_amount', 10, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('item_total', 10, 2);
            $table->decimal('profit_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

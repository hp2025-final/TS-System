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
        Schema::create('dress_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dress_id')->constrained()->onDelete('cascade');
            $table->string('barcode')->unique();
            $table->decimal('size_discount_percentage', 5, 2)->default(0);
            $table->boolean('size_discount_active')->default(false);
            $table->enum('status', ['available', 'sold', 'returned_defective', 'returned_resaleable', 'damaged'])->default('available');
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dress_items');
    }
};

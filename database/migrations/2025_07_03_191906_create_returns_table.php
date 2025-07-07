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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('dress_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('return_reason');
            $table->enum('return_type', ['return', 'exchange']);
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->foreignId('exchange_item_id')->nullable()->constrained('dress_items')->onDelete('set null');
            $table->timestamp('return_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};

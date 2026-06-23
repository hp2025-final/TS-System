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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dress_item_id')->constrained()->onDelete('cascade');
            $table->string('barcode');
            $table->string('collection_name');
            $table->string('dress_name');
            $table->string('size', 50);
            $table->string('status', 50);
            $table->foreignId('scanned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('scan_date')->useCurrent();
            $table->timestamps();
            
            $table->index('barcode');
            $table->index('scan_date');
            $table->index('collection_name');
            $table->index('dress_name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};

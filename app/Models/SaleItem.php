<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'dress_item_id',
        'dress_name',
        'collection_name',
        'sku',
        'barcode',
        'size',
        'fbr_invoice_number',
        'cost_price',
        'sale_price',
        'collection_discount_percentage',
        'dress_discount_percentage',
        'size_discount_percentage',
        'total_discount_amount',
        'tax_percentage',
        'tax_amount',
        'item_total',
        'profit_amount'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'collection_discount_percentage' => 'decimal:2',
        'dress_discount_percentage' => 'decimal:2',
        'size_discount_percentage' => 'decimal:2',
        'total_discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'item_total' => 'decimal:2',
        'profit_amount' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function dressItem(): BelongsTo
    {
        return $this->belongsTo(DressItem::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    public function scopeByCollection($query, $collectionName)
    {
        return $query->where('collection_name', $collectionName);
    }
}

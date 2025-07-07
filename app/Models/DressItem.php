<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DressItem extends Model
{
    protected $fillable = [
        'dress_id',
        'barcode',
        'size',
        'size_discount_percentage',
        'size_discount_active',
        'status',
        'sold_at',
        'returned_at'
    ];

    protected $casts = [
        'size_discount_percentage' => 'decimal:2',
        'size_discount_active' => 'boolean',
        'sold_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function dress(): BelongsTo
    {
        return $this->belongsTo(Dress::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    public function scopeByBarcode($query, $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    public function getFinalPriceAttribute()
    {
        $dress = $this->dress()->with('collection')->first();
        $price = $dress->sale_price;
        
        // Apply collection discount if active
        if ($dress->collection->discount_active && $dress->collection->discount_percentage > 0) {
            $price -= ($dress->sale_price * $dress->collection->discount_percentage / 100);
        }
        
        // Apply dress discount if active
        if ($dress->discount_active && $dress->discount_percentage > 0) {
            $price -= ($dress->sale_price * $dress->discount_percentage / 100);
        }
        
        // Apply size discount if active
        if ($this->size_discount_active && $this->size_discount_percentage > 0) {
            $price -= ($dress->sale_price * $this->size_discount_percentage / 100);
        }
        
        return $price;
    }

    public function getTotalDiscountPercentageAttribute()
    {
        $dress = $this->dress()->with('collection')->first();
        $totalDiscount = 0;
        
        if ($dress->collection->discount_active) {
            $totalDiscount += $dress->collection->discount_percentage;
        }
        
        if ($dress->discount_active) {
            $totalDiscount += $dress->discount_percentage;
        }
        
        if ($this->size_discount_active) {
            $totalDiscount += $this->size_discount_percentage;
        }
        
        return $totalDiscount;
    }
}

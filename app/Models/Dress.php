<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dress extends Model
{
    protected $fillable = [
        'collection_id',
        'name',
        'sku',
        'description',
        'size',
        'hs_code',
        'cost_price',
        'sale_price',
        'discount_percentage',
        'discount_active',
        'tax_percentage',
        'status'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_active' => 'boolean',
        'tax_percentage' => 'decimal:2',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function dressItems(): HasMany
    {
        return $this->hasMany(DressItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithActiveDiscount($query)
    {
        return $query->where('discount_active', true)->where('discount_percentage', '>', 0);
    }

    public function getEffectivePriceAttribute()
    {
        $price = $this->sale_price;
        
        // Apply collection discount if active
        if ($this->collection->discount_active && $this->collection->discount_percentage > 0) {
            $price -= ($price * $this->collection->discount_percentage / 100);
        }
        
        // Apply dress discount if active
        if ($this->discount_active && $this->discount_percentage > 0) {
            $price -= ($price * $this->discount_percentage / 100);
        }
        
        return $price;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'description',
        'discount_percentage',
        'discount_active',
        'status'
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_active' => 'boolean',
    ];

    public function dresses(): HasMany
    {
        return $this->hasMany(Dress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithActiveDiscount($query)
    {
        return $query->where('discount_active', true)->where('discount_percentage', '>', 0);
    }
}

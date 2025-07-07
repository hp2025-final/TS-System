<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'sale_item_id',
        'dress_item_id',
        'user_id',
        'return_reason',
        'return_type',
        'refund_amount',
        'exchange_item_id',
        'return_date',
        'notes'
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'return_date' => 'datetime',
    ];

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function dressItem(): BelongsTo
    {
        return $this->belongsTo(DressItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exchangeItem(): BelongsTo
    {
        return $this->belongsTo(DressItem::class, 'exchange_item_id');
    }

    public function scopeReturns($query)
    {
        return $query->where('return_type', 'return');
    }

    public function scopeExchanges($query)
    {
        return $query->where('return_type', 'exchange');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('return_date', [$startDate, $endDate]);
    }
}

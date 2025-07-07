<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_number',
        'customer_name',
        'customer_phone',
        'subtotal',
        'collection_discount_amount',
        'dress_discount_amount',
        'size_discount_amount',
        'total_discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'sale_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'collection_discount_amount' => 'decimal:2',
        'dress_discount_amount' => 'decimal:2',
        'size_discount_amount' => 'decimal:2',
        'total_discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    public function getTotalProfitAttribute()
    {
        return $this->saleItems->sum('profit_amount');
    }

    public static function generateInvoiceNumber()
    {
        $now = now();
        $year = $now->format('y');
        $month = $now->format('m');
        $day = $now->format('d');
        $hour = $now->format('H');
        $minute = $now->format('i');
        $second = $now->format('s');
        
        return "TS-{$year}{$month}{$day}{$hour}{$minute}{$second}";
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    protected $fillable = [
        'dress_item_id',
        'barcode',
        'collection_name',
        'dress_name',
        'size',
        'status',
        'scanned_by',
        'scan_date',
    ];

    protected $casts = [
        'scan_date' => 'datetime',
    ];

    /**
     * Get the dress item that was scanned
     */
    public function dressItem(): BelongsTo
    {
        return $this->belongsTo(DressItem::class);
    }

    /**
     * Get the user who scanned the item
     */
    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}

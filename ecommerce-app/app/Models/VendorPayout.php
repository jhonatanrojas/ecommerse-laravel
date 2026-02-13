<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'amount',
        'payout_method',
        'status',
        'provider',
        'transaction_reference',
        'meta',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payout_method' => 'array',
        'meta' => 'array',
        'processed_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

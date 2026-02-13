<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'user_id',
        'business_name',
        'document',
        'phone',
        'email',
        'address',
        'status',
        'commission_rate',
        'payout_method',
        'payout_cycle',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'payout_method' => 'array',
        'approved_at' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(VendorProduct::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(VendorOrder::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(VendorPayout::class);
    }

    public function shippingMethods(): HasMany
    {
        return $this->hasMany(VendorShippingMethod::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(VendorDispute::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}

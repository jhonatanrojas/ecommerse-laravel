<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorShippingMethod extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'vendor_id',
        'name',
        'code',
        'base_rate',
        'extra_rate',
        'is_active',
        'rules',
    ];

    protected $casts = [
        'base_rate' => 'decimal:2',
        'extra_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'rules' => 'array',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

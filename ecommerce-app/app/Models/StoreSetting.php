<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'logo',
        'currency',
        'currency_symbol',
        'tax_rate',
        'support_email',
        'transactional_email',
        'maintenance_mode',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'maintenance_mode' => 'boolean',
    ];

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}

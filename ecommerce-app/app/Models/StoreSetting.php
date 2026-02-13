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
        'marketplace_commission_rate',
        'support_email',
        'transactional_email',
        'maintenance_mode',
        'allow_guest_checkout',
        'auto_approve_vendors',
        'auto_approve_vendor_products',
        'enable_automatic_payouts',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'marketplace_commission_rate' => 'decimal:2',
        'maintenance_mode' => 'boolean',
        'allow_guest_checkout' => 'boolean',
        'auto_approve_vendors' => 'boolean',
        'auto_approve_vendor_products' => 'boolean',
        'enable_automatic_payouts' => 'boolean',
    ];

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}

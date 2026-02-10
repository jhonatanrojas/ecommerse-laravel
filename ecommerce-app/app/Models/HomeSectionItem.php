<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HomeSectionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'home_section_id',
        'itemable_type',
        'itemable_id',
        'display_order',
        'configuration',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'configuration' => 'array',
        'display_order' => 'integer',
    ];

    /**
     * Get the home section that owns the item.
     */
    public function homeSection(): BelongsTo
    {
        return $this->belongsTo(HomeSection::class);
    }

    /**
     * Get the parent itemable model (Product, Category, etc.).
     */
    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order items by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}

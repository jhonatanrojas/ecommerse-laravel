<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HomeSection extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'type',
        'title',
        'is_active',
        'display_order',
        'configuration',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (HomeSection $section) {
            if (empty($section->uuid)) {
                $section->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'configuration' => 'array',
        'display_order' => 'integer',
    ];

    /**
     * Get the items for the home section.
     */
    public function items(): HasMany
    {
        return $this->hasMany(HomeSectionItem::class)->orderBy('display_order');
    }

    /**
     * Scope a query to only include active sections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order sections by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Find a section by UUID.
     *
     * @param string $uuid
     * @return self|null
     */
    public static function findByUuid(string $uuid): ?self
    {
        return static::where('uuid', $uuid)->first();
    }

    /**
     * Get the route key for the model.
     * This allows using either ID or UUID in routes.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'id'; // Use 'id' for routing, but UUID is available as optional field
    }
}

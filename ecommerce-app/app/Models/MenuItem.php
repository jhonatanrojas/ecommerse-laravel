<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'url',
        'route_name',
        'route_params',
        'type',
        'target',
        'icon',
        'css_classes',
        'badge_text',
        'badge_color',
        'order',
        'depth',
        'is_active',
        'is_featured',
        'open_in_new_tab',
        'meta',
    ];

    protected $casts = [
        'route_params' => 'array',
        'meta' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class , 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class , 'parent_id')->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootItems($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getComputedUrlAttribute()
    {
        if ($this->type === 'route' && $this->route_name) {
            try {
                return route($this->route_name, $this->route_params ?? []);
            }
            catch (\Exception $e) {
                return '#';
            }
        }
        return $this->url;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}

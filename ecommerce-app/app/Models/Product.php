<?php

namespace App\Models;

use App\Models\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int|null $category_id
 * @property string $name
 * @property string $slug
 * @property string $sku
 * @property string|null $description
 * @property string|null $short_description
 * @property float $price
 * @property float|null $compare_price
 * @property float|null $cost
 * @property int $stock
 * @property int $low_stock_threshold
 * @property float|null $weight
 * @property array|null $dimensions
 * @property bool $is_active
 * @property bool $is_featured
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'compare_price',
        'cost',
        'stock',
        'low_stock_threshold',
        'weight',
        'dimensions',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'dimensions' => 'array', // Cast JSON to array
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include products that are in stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }
}

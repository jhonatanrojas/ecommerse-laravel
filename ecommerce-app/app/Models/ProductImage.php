<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int $product_id
 * @property string $image_path
 * @property string|null $thumbnail_path
 * @property string|null $alt_text
 * @property bool $is_primary
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ProductImage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'image_path',
        'thumbnail_path',
        'alt_text',
        'is_primary',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the product that owns the image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
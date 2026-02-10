<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
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
        'order' => 'integer',
    ];

    /**
     * Boot method to auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (empty($image->uuid)) {
                $image->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id'; // Usar ID numérico para rutas
    }

    /**
     * Get the product that owns the image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the full URL of the image
     */
    public function getUrlAttribute(): string
    {
        // Si image_path es una URL completa, devolverla directamente
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }
        
        // Si no, construir la URL con asset
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get the full URL of the thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }
        
        // Si thumbnail_path es una URL completa, devolverla directamente
        if (filter_var($this->thumbnail_path, FILTER_VALIDATE_URL)) {
            return $this->thumbnail_path;
        }
        
        // Si no, construir la URL con asset
        return asset('storage/' . $this->thumbnail_path);
    }
}
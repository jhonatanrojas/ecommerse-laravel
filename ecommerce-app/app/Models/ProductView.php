<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $viewed_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ProductView extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_views';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'viewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the product that was viewed.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who viewed the product.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
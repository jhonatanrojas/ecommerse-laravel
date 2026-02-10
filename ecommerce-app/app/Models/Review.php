<?php

namespace App\Models;

use App\Models\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int $product_id
 * @property int $user_id
 * @property int|null $order_id
 * @property int $rating
 * @property string|null $title
 * @property string|null $comment
 * @property bool $is_verified_purchase
 * @property bool $is_approved
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $approved_by
 * @property int $helpful_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Review extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'is_verified_purchase',
        'is_approved',
        'approved_at',
        'approved_by',
        'helpful_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the product that owns the review.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that owns the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order that owns the review.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who approved the review.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'uuid');
    }
}
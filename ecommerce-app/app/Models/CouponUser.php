<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $coupon_id
 * @property int $user_id
 * @property int|null $order_id
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CouponUser extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coupon_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'used_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Get the coupon that owns the pivot record.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the user that owns the pivot record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order that owns the pivot record.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
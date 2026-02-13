<?php

namespace App\Models;

use App\Enums\OrderStatus as OrderStatusEnum;
use App\Enums\PaymentStatus;
use App\Models\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $order_number
 * @property OrderStatusEnum $status
 * @property PaymentStatus $payment_status
 * @property float $subtotal
 * @property float $discount
 * @property float $tax
 * @property float $shipping_cost
 * @property float $total
 * @property string|null $coupon_code
 * @property string|null $payment_method
 * @property string|null $shipping_method
 * @property int|null $shipping_address_id
 * @property int|null $billing_address_id
 * @property string|null $notes
 * @property string|null $customer_notes
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Order extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'order_status_id',
        'shipping_status_id',
        'payment_status',
        'subtotal',
        'discount',
        'tax',
        'shipping_cost',
        'total',
        'coupon_code',
        'payment_method',
        'shipping_method',
        'shipping_address_id',
        'billing_address_id',
        'notes',
        'customer_notes',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
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
        'status' => OrderStatusEnum::class,
        'payment_status' => PaymentStatus::class,
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shipping address for the order.
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Get the billing address for the order.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the configured order status for this order.
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    /**
     * Get the configured shipping status for this order.
     */
    public function shippingStatus(): BelongsTo
    {
        return $this->belongsTo(ShippingStatus::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the vendor splits for this order.
     */
    public function vendorOrders(): HasMany
    {
        return $this->hasMany(VendorOrder::class);
    }

    /**
     * Set order status from admin-configured status catalog.
     */
    public function setStatus(OrderStatus $status): self
    {
        $this->order_status_id = $status->id;

        try {
            $this->status = OrderStatusEnum::from($status->slug);
            match ($this->status) {
                OrderStatusEnum::Shipped => $this->shipped_at = now(),
                OrderStatusEnum::Delivered => $this->delivered_at = now(),
                OrderStatusEnum::Cancelled => $this->cancelled_at = now(),
                default => null,
            };
        } catch (\ValueError) {
            // Keep backward compatibility when slug has no enum equivalent.
        }

        if (Auth::guard('admin')->check()) {
            $this->updated_by = Auth::guard('admin')->user()?->uuid;
        }

        $this->save();

        return $this;
    }

    /**
     * Set shipping status from admin-configured status catalog.
     */
    public function setShippingStatus(ShippingStatus $status): self
    {
        $this->shipping_status_id = $status->id;
        $this->save();

        return $this;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Paid
            || $this->payments()->where('status', 'completed')->exists();
    }
}

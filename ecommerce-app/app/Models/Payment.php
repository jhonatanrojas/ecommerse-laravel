<?php

namespace App\Models;

use App\Enums\PaymentRecordStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int $order_id
 * @property string $payment_method
 * @property string|null $transaction_id
 * @property float $amount
 * @property string $currency
 * @property PaymentRecordStatus $status
 * @property array|null $gateway_response
 * @property \Illuminate\Support\Carbon|null $payment_date
 * @property \Illuminate\Support\Carbon|null $refund_date
 * @property float|null $refund_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Payment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'payment_date',
        'refund_date',
        'refund_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'status' => PaymentRecordStatus::class,
        'gateway_response' => 'array', // Cast JSON to array
        'payment_date' => 'datetime',
        'refund_date' => 'datetime',
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
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

<?php

namespace Tests\Feature\Api;

use App\Enums\OrderStatus;
use App\Enums\PaymentRecordStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_process_payment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = $this->createOrderForUser($user, [
            'total' => 150.75,
            'payment_status' => PaymentStatus::Pending,
        ]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->uuid,
            'payment_method' => 'stripe',
            'amount' => 150.75,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.order_id', $order->id)
            ->assertJsonPath('data.payment_method', 'stripe')
            ->assertJsonPath('data.status', PaymentRecordStatus::Completed->value);

        $payment = Payment::query()->firstOrFail();
        $this->assertNotNull($payment->transaction_id);
        $this->assertNotNull($payment->payment_date);

        $order->refresh();
        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);
    }

    public function test_user_cannot_create_payment_for_other_users_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $order = $this->createOrderForUser($owner, ['total' => 120.00]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->uuid,
            'payment_method' => 'paypal',
            'amount' => 120.00,
        ]);

        $response->assertStatus(404);
    }

    public function test_user_cannot_create_payment_if_order_is_already_paid(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = $this->createOrderForUser($user, [
            'total' => 80.00,
            'payment_status' => PaymentStatus::Paid,
        ]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->uuid,
            'payment_method' => 'mercadopago',
            'amount' => 80.00,
        ]);

        $response->assertStatus(422);
    }

    public function test_authenticated_user_can_view_own_payment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = $this->createOrderForUser($user, ['total' => 99.90]);
        $payment = $this->createPaymentForOrder($order, [
            'payment_method' => 'stripe',
            'status' => PaymentRecordStatus::Completed,
            'transaction_id' => 'stripe_txn_1',
        ]);

        $response = $this->getJson("/api/payments/{$payment->uuid}");

        $response->assertStatus(200)
            ->assertJsonPath('data.uuid', $payment->uuid)
            ->assertJsonPath('data.order_id', $order->id);
    }

    public function test_user_cannot_view_other_users_payment(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $order = $this->createOrderForUser($owner, ['total' => 75.00]);
        $payment = $this->createPaymentForOrder($order, ['payment_method' => 'paypal']);

        $response = $this->getJson("/api/payments/{$payment->uuid}");

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_refund_payment_partially_and_fully(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = $this->createOrderForUser($user, [
            'total' => 200.00,
            'payment_status' => PaymentStatus::Paid,
        ]);

        $payment = $this->createPaymentForOrder($order, [
            'payment_method' => 'stripe',
            'amount' => 200.00,
            'status' => PaymentRecordStatus::Completed,
            'transaction_id' => 'stripe_txn_ref_1',
            'payment_date' => now(),
        ]);

        $partialResponse = $this->postJson("/api/payments/{$payment->uuid}/refund", [
            'amount' => 50.00,
        ]);

        $partialResponse->assertStatus(200)
            ->assertJsonPath('data.status', PaymentRecordStatus::PartiallyRefunded->value)
            ->assertJsonPath('data.refund_amount', 50);

        $payment->refresh();
        $order->refresh();
        $this->assertEquals(PaymentRecordStatus::PartiallyRefunded, $payment->status);
        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);

        $fullResponse = $this->postJson("/api/payments/{$payment->uuid}/refund", [
            'amount' => 150.00,
        ]);

        $fullResponse->assertStatus(200)
            ->assertJsonPath('data.status', PaymentRecordStatus::Refunded->value)
            ->assertJsonPath('data.refund_amount', 200);

        $payment->refresh();
        $order->refresh();
        $this->assertEquals(PaymentRecordStatus::Refunded, $payment->status);
        $this->assertEquals(PaymentStatus::Refunded, $order->payment_status);
        $this->assertNotNull($payment->refund_date);
    }

    public function test_webhook_updates_payment_and_order_status(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrderForUser($user, [
            'total' => 130.00,
            'payment_status' => PaymentStatus::Pending,
        ]);

        $payment = $this->createPaymentForOrder($order, [
            'payment_method' => 'stripe',
            'status' => PaymentRecordStatus::Pending,
            'transaction_id' => 'stripe_txn_webhook_1',
        ]);

        $response = $this->postJson('/api/payments/webhook', [
            'type' => 'payment_intent.succeeded',
            'transaction_id' => 'stripe_txn_webhook_1',
            'data' => [
                'object' => [
                    'id' => 'stripe_txn_webhook_1',
                    'metadata' => [
                        'payment_uuid' => $payment->uuid,
                    ],
                ],
            ],
        ], [
            'X-Payment-Gateway' => 'stripe',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('payment_uuid', $payment->uuid)
            ->assertJsonPath('status', PaymentRecordStatus::Completed->value);

        $payment->refresh();
        $order->refresh();
        $this->assertEquals(PaymentRecordStatus::Completed, $payment->status);
        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);
    }

    public function test_webhook_returns_unprocessable_entity_when_gateway_is_missing(): void
    {
        $response = $this->postJson('/api/payments/webhook', [
            'type' => 'payment_intent.succeeded',
        ]);

        $response->assertStatus(422);
    }

    private function createOrderForUser(User $user, array $overrides = []): Order
    {
        static $sequence = 1000;
        $sequence++;

        return Order::create(array_merge([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-'.$sequence,
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
            'subtotal' => 100.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'shipping_cost' => 0.00,
            'total' => 100.00,
            'payment_method' => 'stripe',
            'shipping_method' => 'standard',
            'customer_notes' => 'Payment test order',
        ], $overrides));
    }

    private function createPaymentForOrder(Order $order, array $overrides = []): Payment
    {
        return Payment::create(array_merge([
            'order_id' => $order->id,
            'payment_method' => 'stripe',
            'transaction_id' => null,
            'amount' => $order->total,
            'currency' => 'USD',
            'status' => PaymentRecordStatus::Pending,
            'gateway_response' => null,
            'payment_date' => null,
            'refund_date' => null,
            'refund_amount' => null,
        ], $overrides));
    }
}

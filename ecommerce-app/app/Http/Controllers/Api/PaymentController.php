<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentRequest;
use App\Http\Requests\RefundPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function store(CreatePaymentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $order = $this->findOrderForUser((string) $data['order_id'], (int) Auth::id());

        if (!$order) {
            return response()->json([
                'message' => 'Order not found for this user.',
            ], 404);
        }

        if ($order->payment_status === PaymentStatus::Paid) {
            return response()->json([
                'message' => 'Order is already paid.',
            ], 422);
        }

        $requestedAmount = round((float) $data['amount'], 2);
        $orderTotal = round((float) $order->total, 2);
        if ($requestedAmount !== $orderTotal) {
            return response()->json([
                'message' => 'The amount must match the order total.',
                'data' => [
                    'order_total' => $orderTotal,
                    'requested_amount' => $requestedAmount,
                ],
            ], 422);
        }

        $payment = $this->paymentService->createPayment($order, (string) $data['payment_method']);
        $payment = $this->paymentService->processPayment($payment);

        return response()->json([
            'message' => 'Payment processed.',
            'data' => new PaymentResource($payment),
        ], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $payment = Payment::query()
            ->where('uuid', $uuid)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found.',
            ], 404);
        }

        return response()->json([
            'data' => new PaymentResource($payment),
        ]);
    }

    public function refund(RefundPaymentRequest $request, string $uuid): JsonResponse
    {
        $payment = Payment::query()
            ->where('uuid', $uuid)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found.',
            ], 404);
        }

        try {
            $payment = $this->paymentService->refund($payment, (float) $request->validated('amount'));
        } catch (\DomainException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Refund processed.',
            'data' => new PaymentResource($payment),
        ]);
    }

    private function findOrderForUser(string $identifier, int $userId): ?Order
    {
        $query = Order::query()->where('user_id', $userId);

        if (is_numeric($identifier)) {
            return $query->where('id', (int) $identifier)->first();
        }

        return $query->where('uuid', $identifier)->first();
    }
}


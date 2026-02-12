<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Payments\UnsupportedPaymentGatewayException;
use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentGatewayFactory;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayFactory $paymentGatewayFactory,
        private PaymentService $paymentService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $paymentMethod = $this->resolvePaymentMethod($request);
        if (!$paymentMethod) {
            return response()->json([
                'message' => 'Unable to resolve payment gateway.',
            ], 422);
        }

        try {
            $driver = $this->paymentGatewayFactory->make($paymentMethod);
            $payload = $driver->handleWebhook($request);
            $payment = $this->paymentService->handleWebhook($paymentMethod, $payload);
        } catch (UnsupportedPaymentGatewayException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (AccessDeniedHttpException $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found for webhook payload.',
            ], 404);
        }

        return response()->json([
            'message' => 'Webhook processed.',
            'payment_uuid' => $payment->uuid,
            'status' => $payment->status->value,
        ]);
    }

    private function resolvePaymentMethod(Request $request): ?string
    {
        $headerMethod = $request->header('X-Payment-Gateway');
        if ($headerMethod) {
            return strtolower((string) $headerMethod);
        }

        $payloadMethod = $request->input('gateway')
            ?? $request->input('payment_method')
            ?? $request->input('provider');

        return $payloadMethod ? strtolower((string) $payloadMethod) : null;
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusFromOrderRequest;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderStatusUpdateController extends Controller
{
    public function update(UpdateOrderStatusFromOrderRequest $request, string $orderUuid): RedirectResponse|JsonResponse
    {
        $order = Order::query()->with(['orderStatus', 'payments'])->where('uuid', $orderUuid)->first();

        if (!$order) {
            return $this->errorResponse($request, 'Orden no encontrada.', 404);
        }

        $newStatus = OrderStatus::query()->active()->findOrFail((int) $request->integer('order_status_id'));

        if (!$this->passesPaymentConsistency($order, $newStatus)) {
            return $this->errorResponse(
                $request,
                'No se puede marcar como entregada una orden sin pago confirmado.',
                422
            );
        }

        DB::transaction(function () use ($request, $order, $newStatus) {
            $oldStatus = $order->orderStatus;

            $order->setStatus($newStatus);

            ActivityLog::query()->create([
                'log_name' => 'order-status',
                'description' => 'Estado de orden actualizado desde el panel administrativo',
                'subject_type' => Order::class,
                'subject_id' => $order->id,
                'causer_type' => auth('admin')->user() ? get_class(auth('admin')->user()) : null,
                'causer_id' => auth('admin')->id(),
                'properties' => [
                    'order_uuid' => $order->uuid,
                    'old_status' => $oldStatus?->slug,
                    'old_status_name' => $oldStatus?->name,
                    'new_status' => $newStatus->slug,
                    'new_status_name' => $newStatus->name,
                    'payment_status' => $order->payment_status?->value,
                    'note' => $request->input('note'),
                ],
            ]);
        });

        return $this->successResponse($request, 'Estatus de la orden actualizado correctamente.');
    }

    private function passesPaymentConsistency(Order $order, OrderStatus $newStatus): bool
    {
        if ($newStatus->slug !== 'delivered') {
            return true;
        }

        return $order->isPaid();
    }

    private function successResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    private function errorResponse(Request $request, string $message, int $statusCode): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $statusCode);
        }

        return back()->with('error', $message);
    }
}

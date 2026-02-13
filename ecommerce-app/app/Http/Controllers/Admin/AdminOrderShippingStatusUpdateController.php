<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderShippingStatusRequest;
use App\Models\Order;
use App\Models\ShippingStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminOrderShippingStatusUpdateController extends Controller
{
    public function update(UpdateOrderShippingStatusRequest $request, string $uuid): RedirectResponse|JsonResponse
    {
        $order = Order::query()
            ->with(['shippingStatus', 'orderStatus'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $newStatus = ShippingStatus::query()->findOrFail($request->validated()['shipping_status_id']);

        if (!$newStatus->is_active) {
            return $this->errorResponse($request, 'No puedes asignar un estatus inactivo.', 422);
        }

        DB::transaction(function () use ($order, $newStatus) {
            $oldStatusName = $order->shippingStatus?->name ?? 'Sin estatus';
            
            $order->setShippingStatus($newStatus);

            // Registrar auditoría
            Log::info('Shipping status updated', [
                'order_uuid' => $order->uuid,
                'order_number' => $order->order_number,
                'old_status' => $oldStatusName,
                'new_status' => $newStatus->name,
                'updated_by' => auth()->user()?->name ?? 'Sistema',
            ]);

            // Actualizar timestamps relacionados si es necesario
            if ($newStatus->slug === 'shipped' && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            }

            if ($newStatus->slug === 'delivered' && !$order->delivered_at) {
                $order->update(['delivered_at' => now()]);
            }
        });

        return $this->successResponse($request, 'Estatus de envío actualizado correctamente.');
    }

    private function successResponse($request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    private function errorResponse($request, string $message, int $statusCode = 422): RedirectResponse|JsonResponse
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderStatusRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminOrderStatusController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $statuses = OrderStatus::query()->orderByDesc('is_default')->orderBy('name')->paginate(20);

        if ($request->expectsJson()) {
            return response()->json(OrderStatusResource::collection($statuses));
        }

        return view('admin.order-statuses.index', compact('statuses'));
    }

    public function store(StoreOrderStatusRequest $request): RedirectResponse|JsonResponse
    {
        $payload = $request->validatedPayload();
        $hasDefault = OrderStatus::query()->where('is_default', true)->exists();

        if (!$hasDefault) {
            $payload['is_default'] = true;
            $payload['is_active'] = true;
        }

        DB::transaction(function () use ($payload) {
            if (!empty($payload['is_default'])) {
                OrderStatus::query()->update(['is_default' => false]);
            }

            OrderStatus::query()->create($payload);
        });

        return $this->successResponse($request, 'Estatus creado correctamente.', 201);
    }

    public function update(UpdateOrderStatusRequest $request, int $id): RedirectResponse|JsonResponse
    {
        $status = OrderStatus::query()->findOrFail($id);
        $payload = $request->validatedPayload();

        if ($status->is_default && empty($payload['is_default'])) {
            return $this->errorResponse($request, 'El estatus por defecto no puede quedar sin default.', 422);
        }

        DB::transaction(function () use ($status, $payload) {
            if (!empty($payload['is_default'])) {
                OrderStatus::query()->where('id', '!=', $status->id)->update(['is_default' => false]);
            }

            if (!empty($payload['is_default'])) {
                $payload['is_active'] = true;
            }

            $status->update($payload);
        });

        return $this->successResponse($request, 'Estatus actualizado correctamente.');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $status = OrderStatus::query()->findOrFail($id);

        if ($status->is_default) {
            return $this->errorResponse($request, 'No puedes desactivar el estatus por defecto.', 422);
        }

        $status->update([
            'is_active' => !$status->is_active,
        ]);

        return $this->successResponse($request, 'Estado actualizado correctamente.');
    }

    public function setDefault(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $status = OrderStatus::query()->findOrFail($id);

        DB::transaction(function () use ($status) {
            OrderStatus::query()->update(['is_default' => false]);
            $status->update([
                'is_default' => true,
                'is_active' => true,
            ]);
        });

        return $this->successResponse($request, 'Estatus por defecto actualizado correctamente.');
    }

    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $status = OrderStatus::query()->findOrFail($id);

        if ($status->is_default) {
            return $this->errorResponse($request, 'No puedes eliminar el estatus por defecto.', 422);
        }

        if ($status->orders()->withTrashed()->exists()) {
            return $this->errorResponse($request, 'No puedes eliminar un estatus que está en uso.', 422);
        }

        $status->delete();

        return $this->successResponse($request, 'Estatus eliminado correctamente.');
    }

    private function successResponse(Request $request, string $message, int $statusCode = 200): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ], $statusCode);
        }

        return back()->with('success', $message);
    }

    private function errorResponse(Request $request, string $message, int $statusCode = 422): RedirectResponse|JsonResponse
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

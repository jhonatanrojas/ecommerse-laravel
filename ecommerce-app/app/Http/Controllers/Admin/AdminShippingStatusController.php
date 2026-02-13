<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShippingStatusRequest;
use App\Http\Requests\Admin\UpdateShippingStatusRequest;
use App\Http\Resources\ShippingStatusResource;
use App\Models\ShippingStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminShippingStatusController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $statuses = ShippingStatus::query()
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json(ShippingStatusResource::collection($statuses));
        }

        return view('admin.shipping-statuses.index', compact('statuses'));
    }

    public function store(StoreShippingStatusRequest $request): RedirectResponse|JsonResponse
    {
        $payload = $request->validatedPayload();

        DB::transaction(function () use ($payload) {
            if (!empty($payload['is_default'])) {
                ShippingStatus::query()->update(['is_default' => false]);
            }

            ShippingStatus::query()->create($payload);
        });

        return $this->successResponse($request, 'Estatus de envío creado correctamente.', 201);
    }

    public function update(UpdateShippingStatusRequest $request, int $id): RedirectResponse|JsonResponse
    {
        $status = ShippingStatus::query()->findOrFail($id);
        $payload = $request->validatedPayload();

        DB::transaction(function () use ($status, $payload) {
            if (!empty($payload['is_default'])) {
                ShippingStatus::query()->where('id', '!=', $status->id)->update(['is_default' => false]);
            }

            if (!empty($payload['is_default'])) {
                $payload['is_active'] = true;
            }

            $status->update($payload);
        });

        return $this->successResponse($request, 'Estatus de envío actualizado correctamente.');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $status = ShippingStatus::query()->findOrFail($id);

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
        $status = ShippingStatus::query()->findOrFail($id);

        DB::transaction(function () use ($status) {
            ShippingStatus::query()->update(['is_default' => false]);
            $status->update([
                'is_default' => true,
                'is_active' => true,
            ]);
        });

        return $this->successResponse($request, 'Estatus por defecto actualizado correctamente.');
    }

    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $status = ShippingStatus::query()->findOrFail($id);

        if ($status->is_default) {
            return $this->errorResponse($request, 'No puedes eliminar el estatus por defecto.', 422);
        }

        if ($status->orders()->exists()) {
            return $this->errorResponse($request, 'No puedes eliminar un estatus que está en uso.', 422);
        }

        $status->delete();

        return $this->successResponse($request, 'Estatus de envío eliminado correctamente.');
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

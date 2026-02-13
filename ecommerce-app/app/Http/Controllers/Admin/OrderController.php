<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\OrderStatusServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Class OrderController
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Responsabilidad única: coordinar el flujo entre vistas y servicios
 * No contiene lógica de negocio, solo orquestación
 * 
 * SOLID: Dependency Inversion Principle (DIP)
 * Depende de interfaces (OrderServiceInterface, OrderStatusServiceInterface)
 * No depende de implementaciones concretas
 * 
 * SOLID: Open/Closed Principle (OCP)
 * Abierto para extensión mediante inyección de dependencias
 * Cerrado para modificación: no necesita cambios si se agregan nuevas funcionalidades
 */
class OrderController extends Controller
{
    public function __construct(
        protected OrderServiceInterface $orderService,
        protected OrderStatusServiceInterface $statusService,
        protected \App\Services\Contracts\PaymentStatusServiceInterface $paymentStatusService
    ) {}

    /**
     * Display a listing of orders with filters
     */
    public function index(FilterOrderRequest $request): View
    {
        $filters = $request->getFilters();
        $perPage = $request->input('per_page', 15);

        $orders = $this->orderService->getPaginatedOrders($perPage, $filters);
        $statistics = $this->orderService->getOrderStatistics();
        $statuses = $this->statusService->getAllStatuses();

        return view('admin.orders.index', compact('orders', 'statistics', 'statuses', 'filters'));
    }

    /**
     * Display the specified order
     */
    public function show(string $uuid): View
    {
        $order = $this->orderService->getOrderByUuid($uuid);

        if (!$order) {
            abort(404, 'Orden no encontrada');
        }

        $availableStatuses = $this->statusService->getAvailableStatuses($order);

        // Obtener el pago más reciente de la orden
        $payment = $order->payments()->latest()->first();
        $availablePaymentStatuses = $payment ? $this->paymentStatusService->getAvailableStatuses($payment) : [];

        return view('admin.orders.show', compact('order', 'availableStatuses', 'payment', 'availablePaymentStatuses'));
    }


    /**
     * Update the specified order
     */
    public function update(UpdateOrderRequest $request, string $uuid): RedirectResponse
    {
        $order = $this->orderService->getOrderByUuid($uuid);

        if (!$order) {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', 'Orden no encontrada');
        }

        // Si se está actualizando el estado, usar el servicio de estados
        if ($request->has('status')) {
            $newStatus = OrderStatus::from($request->input('status'));
            
            if (!$this->statusService->canChangeStatus($order, $newStatus)) {
                return redirect()
                    ->back()
                    ->with('error', 'No se puede cambiar al estado seleccionado desde el estado actual');
            }

            $this->statusService->changeStatus($order, $newStatus);
        }

        // Actualizar otros campos
        $dataToUpdate = $request->except('status');
        if (!empty($dataToUpdate)) {
            $this->orderService->updateOrder($uuid, $dataToUpdate);
        }

        return redirect()
            ->route('admin.orders.show', $uuid)
            ->with('success', 'Orden actualizada correctamente');
    }

    /**
     * Cancel the specified order
     */
    public function destroy(string $uuid): RedirectResponse
    {
        $order = $this->orderService->getOrderByUuid($uuid);

        if (!$order) {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', 'Orden no encontrada');
        }

        // Verificar si se puede cancelar
        if (!$this->statusService->canChangeStatus($order, OrderStatus::Cancelled)) {
            return redirect()
                ->back()
                ->with('error', 'No se puede cancelar esta orden en su estado actual');
        }

        $this->orderService->cancelOrder($uuid, 'Cancelado desde el panel administrativo');

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Orden cancelada correctamente');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(\App\Http\Requests\Admin\UpdatePaymentStatusRequest $request, string $uuid): RedirectResponse
    {
        $order = $this->orderService->getOrderByUuid($uuid);

        if (!$order) {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', 'Orden no encontrada');
        }

        $payment = $order->payments()->latest()->first();

        if (!$payment) {
            return redirect()
                ->back()
                ->with('error', 'No se encontró un pago asociado a esta orden');
        }

        $newStatus = \App\Enums\PaymentRecordStatus::from($request->input('status'));

        if (!$this->paymentStatusService->canChangeStatus($payment, $newStatus)) {
            return redirect()
                ->back()
                ->with('error', 'No se puede cambiar al estado seleccionado desde el estado actual');
        }

        $success = $this->paymentStatusService->changeStatus(
            $payment,
            $newStatus,
            $request->input('admin_note')
        );

        if ($success) {
            return redirect()
                ->route('admin.orders.show', $uuid)
                ->with('success', 'Estado del pago actualizado correctamente');
        }

        return redirect()
            ->back()
            ->with('error', 'Error al actualizar el estado del pago');
    }

}

@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Orden #{{ $order->order_number }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Creada el {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
            Volver al Listado
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    
    <!-- Columna Principal -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Items de la Orden -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Productos</h2>
            
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Producto</th>
                            <th scope="col" class="px-6 py-3">SKU</th>
                            <th scope="col" class="px-6 py-3">Cantidad</th>
                            <th scope="col" class="px-6 py-3">Precio</th>
                            <th scope="col" class="px-6 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $item->product_name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item->product_sku ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4">
                                ${{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 font-semibold">
                                ${{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totales -->
            <div class="mt-6 space-y-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Descuento:</span>
                    <span class="font-medium text-red-600 dark:text-red-500">-${{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Impuestos:</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Envío:</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                    <span class="text-gray-900 dark:text-white">Total:</span>
                    <span class="text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Información del Cliente -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Información del Cliente</h2>
            
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->user->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Email:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Direcciones -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Dirección de Envío -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Dirección de Envío</h2>
                @if($order->shippingAddress)
                <div class="text-sm text-gray-900 dark:text-white space-y-1">
                    <p>{{ $order->shippingAddress->address_line1 }}</p>
                    @if($order->shippingAddress->address_line2)
                    <p>{{ $order->shippingAddress->address_line2 }}</p>
                    @endif
                    <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}</p>
                    <p>{{ $order->shippingAddress->postal_code }}</p>
                    <p>{{ $order->shippingAddress->country }}</p>
                </div>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No especificada</p>
                @endif
            </div>

            <!-- Dirección de Facturación -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Dirección de Facturación</h2>
                @if($order->billingAddress)
                <div class="text-sm text-gray-900 dark:text-white space-y-1">
                    <p>{{ $order->billingAddress->address_line1 }}</p>
                    @if($order->billingAddress->address_line2)
                    <p>{{ $order->billingAddress->address_line2 }}</p>
                    @endif
                    <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }}</p>
                    <p>{{ $order->billingAddress->postal_code }}</p>
                    <p>{{ $order->billingAddress->country }}</p>
                </div>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No especificada</p>
                @endif
            </div>
        </div>

    </div>

    <!-- Columna Lateral -->
    <div class="space-y-6">
        
        <!-- Estatus de la Orden -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Estatus de la Orden</h2>
            
            <form method="POST" action="{{ route('admin.orders.status.update', $order->uuid) }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado Actual</label>
                    @include('admin.orders.partials.status-badge', ['status' => $order->orderStatus ?? $order->status])
                </div>

                @if(count($availableStatuses) > 0)
                <div class="mb-4">
                    <label for="order_status_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cambiar a:</label>
                    <select id="order_status_id" name="order_status_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Seleccionar...</option>
                        @foreach($availableStatuses as $statusOption)
                            <option value="{{ $statusOption->id }}" {{ (int) old('order_status_id') === $statusOption->id ? 'selected' : '' }}>
                                {{ $statusOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Actualizar estatus
                </button>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay cambios de estado disponibles</p>
                @endif
            </form>
        </div>

        <!-- Estatus de Envío -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Estatus de Envío</h2>
            
            <form method="POST" action="{{ route('admin.orders.shipping-status.update', $order->uuid) }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado Actual</label>
                    @if($order->shippingStatus)
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full" style="background-color: {{ $order->shippingStatus->color }}22; color: {{ $order->shippingStatus->color }};">
                            <span class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $order->shippingStatus->color }};"></span>
                            {{ $order->shippingStatus->name }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-700">
                            Sin estatus
                        </span>
                    @endif
                </div>

                @php
                    $availableShippingStatuses = \App\Models\ShippingStatus::active()->ordered()->get();
                @endphp

                @if($availableShippingStatuses->count() > 0)
                <div class="mb-4">
                    <label for="shipping_status_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cambiar a:</label>
                    <select id="shipping_status_id" name="shipping_status_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Seleccionar...</option>
                        @foreach($availableShippingStatuses as $statusOption)
                            <option value="{{ $statusOption->id }}" 
                                    {{ (int) old('shipping_status_id', $order->shipping_status_id) === $statusOption->id ? 'selected' : '' }}
                                    data-color="{{ $statusOption->color }}">
                                {{ $statusOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 focus:outline-none dark:focus:ring-purple-800">
                    Actualizar estatus de envío
                </button>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay estatus de envío configurados</p>
                @endif
            </form>
        </div>

        <!-- Estado de Pago -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Estado de Pago</h2>
            
            @if($payment)
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado Actual</label>
                    @include('admin.orders.partials.payment-record-badge', ['status' => $payment->status])
                </div>

                @if($payment->transaction_id)
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID de Transacción:</span>
                    <p class="text-sm text-gray-900 dark:text-white font-mono">{{ $payment->transaction_id }}</p>
                </div>
                @endif

                @if($payment->payment_date)
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Pago:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $payment->payment_date->format('d/m/Y H:i') }}</p>
                </div>
                @endif

                @if($payment->refund_date)
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Reembolso:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $payment->refund_date->format('d/m/Y H:i') }}</p>
                </div>
                @endif

                @if($payment->refund_amount)
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Monto Reembolsado:</span>
                    <p class="text-sm text-gray-900 dark:text-white">${{ number_format($payment->refund_amount, 2) }}</p>
                </div>
                @endif

                @if(count($availablePaymentStatuses) > 0)
                <form method="POST" action="{{ route('admin.orders.payment-status.update', $order->uuid) }}" id="payment-status-form">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <label for="payment_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cambiar Estado:</label>
                        <select id="payment_status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Seleccionar...</option>
                            @foreach($availablePaymentStatuses as $status)
                                <option value="{{ $status->value }}">{{ ucfirst(str_replace('_', ' ', $status->value)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="admin_note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nota (opcional):</label>
                        <textarea id="admin_note" name="admin_note" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Razón del cambio de estado..."></textarea>
                    </div>

                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Actualizar Estado de Pago
                    </button>
                </form>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">No hay cambios de estado disponibles</p>
                @endif
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay información de pago disponible</p>
            @endif
            
            @if($order->payment_method)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Método de Pago:</span>
                <p class="text-sm text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</p>
            </div>
            @endif
        </div>

        <!-- Información Adicional -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Información Adicional</h2>
            
            <div class="space-y-3 text-sm">
                @if($order->coupon_code)
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">Cupón:</span>
                    <p class="text-gray-900 dark:text-white">{{ $order->coupon_code }}</p>
                </div>
                @endif

                @if($order->shipping_method)
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">Método de Envío:</span>
                    <p class="text-gray-900 dark:text-white">{{ $order->shipping_method }}</p>
                </div>
                @endif

                @if($order->shipped_at)
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">Enviado:</span>
                    <p class="text-gray-900 dark:text-white">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif

                @if($order->delivered_at)
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">Entregado:</span>
                    <p class="text-gray-900 dark:text-white">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif

                @if($order->notes)
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">Notas:</span>
                    <p class="text-gray-900 dark:text-white">{{ $order->notes }}</p>
                </div>
                @endif

                @if($order->customer_notes)
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">Notas del Cliente:</span>
                    <p class="text-gray-900 dark:text-white">{{ $order->customer_notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Acciones -->
        @if(collect($availableStatuses)->contains(fn($s) => ($s->slug ?? $s->value ?? null) === \App\Enums\OrderStatus::Cancelled->value))
        <form method="POST" action="{{ route('admin.orders.destroy', $order->uuid) }}" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta orden?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                Cancelar Orden
            </button>
        </form>
        @endif

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentStatusForm = document.getElementById('payment-status-form');
    
    if (paymentStatusForm) {
        paymentStatusForm.addEventListener('submit', function(e) {
            const selectedStatus = document.getElementById('payment_status').value;
            
            // Confirmación para estados críticos
            if (selectedStatus === 'completed') {
                if (!confirm('¿Estás seguro de marcar este pago como completado? Esta acción actualizará el estado de la orden.')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            if (selectedStatus === 'refunded' || selectedStatus === 'partially_refunded') {
                if (!confirm('¿Estás seguro de procesar este reembolso? Asegúrate de haber procesado el reembolso en la pasarela de pago.')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    }
});
</script>
@endsection

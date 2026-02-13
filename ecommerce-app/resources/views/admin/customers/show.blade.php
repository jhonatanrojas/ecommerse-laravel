@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Detalle del Cliente</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $customer->user?->name }} - {{ $customer->user?->email }}</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700">
            Volver al listado
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-3">
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total gastado</p>
        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalSpent, 2) }}</p>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Última compra</p>
        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
            {{ $lastPurchase ? $lastPurchase->created_at->format('d/m/Y H:i') : 'Sin compras' }}
        </p>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Órdenes totales</p>
        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalOrders }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Información del cliente</h2>

            <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->user?->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->user?->email) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Teléfono</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone ?? $customer->user?->phone) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="document" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Documento</label>
                    <input type="text" id="document" name="document" value="{{ old('document', $customer->document) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="birthdate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha de nacimiento</label>
                    <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate', optional($customer->birthdate)->format('Y-m-d')) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Órdenes</h2>
                <a href="{{ route('admin.customers.orders.index', $customer->id) }}" class="text-sm text-blue-600 hover:underline" target="_blank">Ver JSON</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Nº Orden</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">@include('admin.orders.partials.status-badge', ['status' => $order->orderStatus ?? $order->status])</td>
                                <td class="px-4 py-3 font-semibold">${{ number_format($order->total, 2) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.orders.show', $order->uuid) }}" class="text-blue-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Este cliente no tiene órdenes registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Estado del cliente</h2>

            <div class="mb-4">
                @if($customer->user?->is_active)
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Activo</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Inactivo</span>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.customers.toggle', $customer->id) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full text-white {{ $customer->user?->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    {{ $customer->user?->is_active ? 'Desactivar cliente' : 'Activar cliente' }}
                </button>
            </form>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Direcciones</h2>
                <a href="{{ route('admin.customers.addresses.index', $customer->id) }}" class="text-sm text-blue-600 hover:underline" target="_blank">Ver JSON</a>
            </div>

            <div class="space-y-4">
                @forelse($addresses as $address)
                    <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                        <div class="flex items-center flex-wrap gap-2 mb-2">
                            <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">{{ strtoupper($address->type->value) }}</span>
                            @if($customer->default_shipping_address_id === $address->id)
                                <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Envío predeterminada</span>
                            @endif
                            @if($customer->default_billing_address_id === $address->id)
                                <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Facturación predeterminada</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $address->first_name }} {{ $address->last_name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $address->address_line1 }}{{ $address->address_line2 ? ', ' . $address->address_line2 : '' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $address->country }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Este cliente no tiene direcciones registradas.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

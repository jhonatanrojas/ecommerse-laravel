@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Gestión de Órdenes</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Administra todas las órdenes del ecommerce</p>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total'] }}</span>
            </div>
            <div class="flex-1 ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Órdenes</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-500">{{ $statistics['pending'] }}</span>
            </div>
            <div class="flex-1 ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendientes</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl font-bold text-green-600 dark:text-green-500">{{ $statistics['delivered'] }}</span>
            </div>
            <div class="flex-1 ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Entregadas</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="text-2xl font-bold text-blue-600 dark:text-blue-500">${{ number_format($statistics['total_revenue'], 2) }}</span>
            </div>
            <div class="flex-1 ml-3">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ingresos Totales</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
        
        <!-- Estado -->
        <div>
            <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
            <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                <option value="">Todos</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->slug }}" {{ request('status') === $status->slug ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Fecha Inicio -->
        <div>
            <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha Inicio</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        </div>

        <!-- Fecha Fin -->
        <div>
            <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha Fin</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        </div>

        <!-- Cliente -->
        <div>
            <label for="customer" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cliente</label>
            <input type="text" id="customer" name="customer" value="{{ request('customer') }}" placeholder="Nombre o email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        </div>

        <!-- Número de Orden -->
        <div>
            <label for="order_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nº Orden</label>
            <input type="text" id="order_number" name="order_number" value="{{ request('order_number') }}" placeholder="ORD-00001" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        </div>

        <!-- Botones -->
        <div class="flex items-end gap-2 md:col-span-5">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Filtrar
            </button>
            <a href="{{ route('admin.orders.index') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Tabla de Órdenes -->
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Nº Orden</th>
                <th scope="col" class="px-6 py-3">Cliente</th>
                <th scope="col" class="px-6 py-3">Fecha</th>
                <th scope="col" class="px-6 py-3">Total</th>
                <th scope="col" class="px-6 py-3">Estado</th>
                <th scope="col" class="px-6 py-3">Pago</th>
                <th scope="col" class="px-6 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                    {{ $order->order_number }}
                </td>
                <td class="px-6 py-4">
                    <div>
                        <div class="font-medium">{{ $order->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-6 py-4 font-semibold">
                    ${{ number_format($order->total, 2) }}
                </td>
                <td class="px-6 py-4">
                    @include('admin.orders.partials.status-badge', ['status' => $order->orderStatus ?? $order->status])
                </td>
                <td class="px-6 py-4">
                    @include('admin.orders.partials.payment-badge', ['status' => $order->payment_status])
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.orders.show', $order->uuid) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                        Ver Detalle
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    No se encontraron órdenes
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginación -->
<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection

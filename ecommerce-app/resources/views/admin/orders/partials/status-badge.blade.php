@php
    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'returned' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ];

    $statusLabels = [
        'pending' => 'Pendiente',
        'processing' => 'Procesando',
        'shipped' => 'Enviado',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
        'returned' => 'Devuelto',
    ];

    $statusValue = is_object($status) ? $status->value : $status;
    $class = $statusClasses[$statusValue] ?? 'bg-gray-100 text-gray-800';
    $label = $statusLabels[$statusValue] ?? ucfirst($statusValue);
@endphp

<span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $class }}">
    {{ $label }}
</span>

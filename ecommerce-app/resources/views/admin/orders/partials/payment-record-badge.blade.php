@php
    $badgeClasses = match($status->value) {
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'refunded' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        'partially_refunded' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };

    $statusLabel = match($status->value) {
        'pending' => 'Pendiente',
        'completed' => 'Completado',
        'failed' => 'Fallido',
        'refunded' => 'Reembolsado',
        'partially_refunded' => 'Reembolso Parcial',
        default => ucfirst($status->value),
    };
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
    {{ $statusLabel }}
</span>

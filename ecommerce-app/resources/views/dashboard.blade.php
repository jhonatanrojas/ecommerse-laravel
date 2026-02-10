@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Resumen general de tu tienda online.
            </p>
        </div>
    </div>
@endsection

@section('content')
    {{-- Cards de métricas (ejemplo basado en Flowbite Admin) --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="bg-white rounded-lg shadow dark:bg-gray-800 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-primary-100 dark:bg-primary-900/40">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v2m8-8a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>
                </div>
                <div class="ms-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Ventas de hoy
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        $0.00
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow dark:bg-gray-800 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-emerald-100 dark:bg-emerald-900/40">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v4h4M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ms-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Órdenes completadas
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        0
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow dark:bg-gray-800 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/40">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="ms-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Clientes registrados
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        0
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow dark:bg-gray-800 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-amber-100 dark:bg-amber-900/40">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 7v14m6-14v14" />
                    </svg>
                </div>
                <div class="ms-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Productos activos
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        0
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de ejemplo estilo Flowbite para actividades recientes --}}
    <div class="mt-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Actividad reciente
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Órdenes, registros y eventos más recientes en tu ecommerce.
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3">Tipo</th>
                        <th scope="col" class="px-4 py-3">Descripción</th>
                        <th scope="col" class="px-4 py-3">Fecha</th>
                        <th scope="col" class="px-4 py-3 text-right">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-4 py-3 whitespace-nowrap">Sistema</td>
                        <td class="px-4 py-3">Tu panel de administración está listo con Flowbite.</td>
                        <td class="px-4 py-3">{{ now()->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 rounded-full dark:bg-emerald-900/40 dark:text-emerald-300">
                                Activo
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

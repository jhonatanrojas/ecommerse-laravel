<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Vendor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('vendor.dashboard') }}" class="font-semibold text-lg">Vendor Panel</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('vendor.products.index') }}">Productos</a>
                <a href="{{ route('vendor.orders.index') }}">Órdenes</a>
                <a href="{{ route('vendor.payouts.index') }}">Liquidaciones</a>
                <a href="{{ route('vendor.shipping-methods.index') }}">Envíos</a>
                <a href="{{ route('vendor.disputes.index') }}">Disputas</a>
                <a href="{{ route('vendor.profile.edit') }}">Perfil</a>
                <form method="POST" action="{{ route('vendor.logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-4">
        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 rounded bg-red-100 text-red-700">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>

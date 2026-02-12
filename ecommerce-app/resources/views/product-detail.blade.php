<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mi Tienda') }} - Producto</title>
    <meta name="description" content="Detalle de producto con variantes, stock e informacion completa.">

    @vite(['resources/css/app.css', 'resources/js/product-detail-app.js'])
</head>
<body class="antialiased bg-white text-gray-900">
    <div id="product-detail-app"></div>
</body>
</html>

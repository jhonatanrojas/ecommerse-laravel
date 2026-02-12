<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mi Tienda') }} - Tienda</title>
    <meta name="description" content="Catalogo de productos con filtros, ordenamiento y paginacion.">

    @vite(['resources/css/app.css', 'resources/js/products-app.js'])
</head>
<body class="antialiased bg-white text-gray-900">
    <div id="products-app"></div>
</body>
</html>

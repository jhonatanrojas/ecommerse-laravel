<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mi Tienda') }} - Productos por categoría</title>
    <meta name="description" content="Lista de productos por categoría con filtros y paginación.">

    @vite(['resources/css/app.css', 'resources/js/category-products-app.js'])
</head>
<body class="antialiased bg-white text-gray-900">
    <div id="category-products-app"></div>
</body>
</html>

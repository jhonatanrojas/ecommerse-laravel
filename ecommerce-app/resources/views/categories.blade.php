<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mi Tienda') }} - Categorías</title>
    <meta name="description" content="Explora las categorías de productos de nuestra tienda.">

    @vite(['resources/css/app.css', 'resources/js/categories-app.js'])
</head>
<body class="antialiased bg-white text-gray-900">
    <div id="categories-app"></div>
</body>
</html>

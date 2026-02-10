<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>{{ config('app.name', 'Mi Tienda') }} — Tienda en línea | Envío gratis desde $49</title>
    <meta name="description" content="Descubre productos exclusivos con la mejor calidad y precio. Envío gratis desde $49, devoluciones fáciles y soporte 24/7.">
    <meta name="robots" content="index, follow">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ config('app.name', 'Mi Tienda') }} — Tienda en línea">
    <meta property="og:description" content="Descubre productos exclusivos con la mejor calidad y precio.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">

    {{-- Fonts — Preconnect for speed --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/home-app.js'])

    {{-- Critical CSS for LCP --}}
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; margin: 0; }
        .hero-section { min-height: 85vh; }
    </style>
</head>
<body class="antialiased bg-white text-gray-900">
    <div id="home-app"></div>
</body>
</html>

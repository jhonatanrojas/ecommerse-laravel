<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $page->meta_title ?: $page->title }} - {{ config('app.name', 'Mi Tienda') }}</title>
    <meta name="description" content="{{ $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160) }}">
    @if($page->meta_keywords)
        <meta name="keywords" content="{{ $page->meta_keywords }}">
    @endif
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
    <meta property="og:description" content="{{ $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">

    @vite(['resources/css/app.css'])

    <style>
        .cms-content h1, .cms-content h2, .cms-content h3, .cms-content h4, .cms-content h5, .cms-content h6 { margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 700; color: #111827; }
        .cms-content h1 { font-size: 2rem; }
        .cms-content h2 { font-size: 1.5rem; }
        .cms-content h3 { font-size: 1.25rem; }
        .cms-content p { margin-bottom: 1rem; line-height: 1.75; color: #1f2937; }
        .cms-content ul, .cms-content ol { margin: 1rem 0; padding-left: 1.5rem; color: #1f2937; }
        .cms-content a { color: #1d4ed8; text-decoration: underline; }
        .cms-content img { margin: 1rem 0; max-width: 100%; border-radius: 0.5rem; }
        .cms-content blockquote { margin: 1rem 0; padding-left: 1rem; border-left: 4px solid #d1d5db; color: #374151; }
        .cms-content table { width: 100%; margin: 1rem 0; border-collapse: collapse; }
        .cms-content table td, .cms-content table th { border: 1px solid #e5e7eb; padding: 0.5rem; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <article class="bg-white shadow-sm rounded-lg p-6 sm:p-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $page->title }}</h1>

            @if($page->published_at)
                <p class="mt-2 text-sm text-gray-500">Publicado el {{ $page->published_at->format('Y-m-d H:i') }}</p>
            @endif

            <div class="cms-content mt-8">
                {!! $page->content !!}
            </div>
        </article>
    </main>
</body>
</html>

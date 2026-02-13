<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Mi Tienda'))</title>
    <meta name="description" content="@yield('meta_description', 'Contenido e informacion de la tienda.')">
    @hasSection('meta_keywords')
        <meta name="keywords" content="@yield('meta_keywords')">
    @endif
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">

    @stack('meta')

    @vite(['resources/css/app.css'])

    <style>
        .cms-content h1, .cms-content h2, .cms-content h3, .cms-content h4, .cms-content h5, .cms-content h6 { margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 700; color: #111827; }
        .cms-content h1 { font-size: 2rem; }
        .cms-content h2 { font-size: 1.5rem; }
        .cms-content h3 { font-size: 1.25rem; }
        .cms-content p { margin-bottom: 1rem; line-height: 1.75; color: #1f2937; }
        .cms-content ul, .cms-content ol { margin: 1rem 0; padding-left: 1.5rem; color: #1f2937; }
        .cms-content a { color: #4f46e5; text-decoration: underline; }
        .cms-content img { margin: 1rem 0; max-width: 100%; border-radius: 0.5rem; }
        .cms-content blockquote { margin: 1rem 0; padding-left: 1rem; border-left: 4px solid #d1d5db; color: #374151; }
        .cms-content table { width: 100%; margin: 1rem 0; border-collapse: collapse; }
        .cms-content table td, .cms-content table th { border: 1px solid #e5e7eb; padding: 0.5rem; }
    </style>
</head>
<body class="min-h-screen bg-white text-gray-900 antialiased">
    <header class="sticky top-0 z-50 bg-white/95 shadow-sm backdrop-blur-md">
        <div class="container mx-auto px-4">
            <div class="flex h-16 items-center justify-between gap-4">
                <a href="/" class="text-xl font-extrabold tracking-tight" style="color: var(--color-primary);">
                    {{ config('app.name', 'Mi Tienda') }}
                </a>

                <nav class="hidden lg:flex items-center gap-1">
                    @forelse($headerMenuItems ?? [] as $item)
                        <a href="{{ $item->computed_url ?: '#' }}"
                           @if(($item->open_in_new_tab ?? false) || ($item->target === '_blank')) target="_blank" rel="noopener noreferrer" @endif
                           class="px-3 py-2 text-sm font-medium text-gray-700 rounded-lg transition-colors duration-200 hover:bg-gray-100 hover:text-indigo-700">
                            {{ $item->label }}
                        </a>
                    @empty
                        <a href="/" class="px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-indigo-700">Inicio</a>
                        <a href="/products" class="px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-indigo-700">Tienda</a>
                        <a href="/categories" class="px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-indigo-700">Categorías</a>
                        <a href="/pages" class="px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-indigo-700">Páginas</a>
                    @endforelse
                </nav>

                <div class="flex items-center gap-2">
                    <button id="mobile-menu-toggle"
                            type="button"
                            class="lg:hidden rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900"
                            aria-label="Abrir menú"
                            aria-controls="mobile-menu"
                            aria-expanded="false">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <a href="{{ auth('customer')->check() ? '/customer' : '/login' }}" class="rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900" aria-label="Mi cuenta">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden border-t border-gray-100 bg-white lg:hidden">
            <nav class="container mx-auto px-4 py-3 space-y-1">
                @forelse($headerMenuItems ?? [] as $item)
                    <a href="{{ $item->computed_url ?: '#' }}"
                       @if(($item->open_in_new_tab ?? false) || ($item->target === '_blank')) target="_blank" rel="noopener noreferrer" @endif
                       class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-100 hover:text-indigo-700">
                        {{ $item->label }}
                    </a>
                @empty
                    <a href="/" class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-indigo-700">Inicio</a>
                    <a href="/products" class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-indigo-700">Tienda</a>
                    <a href="/categories" class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-indigo-700">Categorías</a>
                    <a href="/pages" class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-indigo-700">Páginas</a>
                @endforelse
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-white mt-12">
        <div class="container mx-auto px-4 py-10 md:py-12">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div>
                    <p class="text-lg font-bold">{{ config('app.name', 'Mi Tienda') }}</p>
                    <p class="mt-2 text-sm text-gray-400">Contenido informativo, políticas y páginas institucionales.</p>
                </div>
                <div>
                    <p class="mb-3 text-sm font-bold uppercase tracking-wider text-white">Navegación</p>
                    <div class="space-y-2">
                        @forelse($footerMenuItems ?? [] as $item)
                            <a href="{{ $item->computed_url ?: '#' }}"
                               @if(($item->open_in_new_tab ?? false) || ($item->target === '_blank')) target="_blank" rel="noopener noreferrer" @endif
                               class="block text-sm text-gray-400 hover:text-white transition-colors duration-200">
                                {{ $item->label }}
                            </a>
                        @empty
                            <a href="/" class="block text-sm text-gray-400 hover:text-white">Inicio</a>
                            <a href="/products" class="block text-sm text-gray-400 hover:text-white">Tienda</a>
                            <a href="/pages" class="block text-sm text-gray-400 hover:text-white">Páginas</a>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="mb-3 text-sm font-bold uppercase tracking-wider text-white">Soporte</p>
                    <p class="text-sm text-gray-400">info@tutienda.com</p>
                    <p class="text-sm text-gray-400">(123) 456-7890</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (!toggleButton || !mobileMenu) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const isHidden = mobileMenu.classList.contains('hidden');
                mobileMenu.classList.toggle('hidden');
                toggleButton.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            });
        });
    </script>
</body>
</html>

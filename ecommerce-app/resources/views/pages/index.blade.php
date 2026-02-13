@extends('layouts.frontend-cms')

@section('title', 'Páginas - ' . config('app.name', 'Mi Tienda'))
@section('meta_description', 'Páginas publicadas de información y contenido del sitio.')

@section('content')
    <main class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Páginas</h1>
            <p class="mt-2 text-gray-600">Contenido publicado del CMS.</p>
        </div>

        @if($pages->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pages as $page)
                    <article class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <a href="{{ route('pages.show', $page->slug) }}" class="hover:text-blue-700">
                                {{ $page->title }}
                            </a>
                        </h2>

                        <p class="mt-2 text-sm text-gray-500">
                            {{ $page->published_at?->format('Y-m-d') ?? $page->created_at?->format('Y-m-d') }}
                        </p>

                        <p class="mt-3 text-gray-700">
                            {{ \Illuminate\Support\Str::limit(strip_tags($page->meta_description ?: $page->content), 150) }}
                        </p>

                        <a href="{{ route('pages.show', $page->slug) }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium">
                            Leer más
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $pages->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg border border-gray-200 p-8 text-center text-gray-600">
                No hay páginas publicadas por el momento.
            </div>
        @endif
    </main>
@endsection

@extends('layouts.frontend-cms')

@section('title', ($page->meta_title ?: $page->title) . ' - ' . config('app.name', 'Mi Tienda'))
@section('meta_description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160))
@section('meta_keywords', $page->meta_keywords)

@push('meta')
    <meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
    <meta property="og:description" content="{{ $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
@endpush

@section('content')
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
@endsection

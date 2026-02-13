@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Páginas</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Administra contenido CMS y estado de publicación.</p>
    </div>

    @can('manage_pages')
        <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2">
            Nueva Página
        </a>
    @endcan
</div>

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
    <form method="GET" action="{{ route('admin.pages.index') }}" class="flex flex-col gap-3 md:flex-row md:items-center">
        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por título o slug..."
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full md:w-96 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2.5">
            Buscar
        </button>

        <a href="{{ route('admin.pages.index') }}" class="text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center">
            Limpiar
        </a>
    </form>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Título</th>
                <th scope="col" class="px-6 py-3">Slug</th>
                <th scope="col" class="px-6 py-3">Estado</th>
                <th scope="col" class="px-6 py-3">Publicado en</th>
                <th scope="col" class="px-6 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pages as $page)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $page->title }}</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">/{{ $page->slug }}</td>
                    <td class="px-6 py-4">
                        @if ($page->is_published)
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">Publicado</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-300">Borrador</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ optional($page->published_at)->format('Y-m-d H:i') ?? '-' }}</td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                        @canany(['edit_pages', 'manage_pages'])
                            <a href="{{ route('admin.pages.edit', $page->uuid) }}" class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>

                            <form action="{{ route('admin.pages.toggle', $page->uuid) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="{{ $page->is_published ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }} font-medium">
                                    {{ $page->is_published ? 'Despublicar' : 'Publicar' }}
                                </button>
                            </form>
                        @endcanany

                        @canany(['delete_pages', 'manage_pages'])
                            <form action="{{ route('admin.pages.destroy', $page->uuid) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta página?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                            </form>
                        @endcanany
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No hay páginas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $pages->links() }}
</div>
@endsection

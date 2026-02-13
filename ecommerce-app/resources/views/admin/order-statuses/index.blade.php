@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Estatus de Órdenes</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configura y administra los estatus del flujo de órdenes.</p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-1">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Nuevo Estatus</h2>
            <form method="POST" action="{{ route('admin.order-statuses.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="ej: processing" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label for="color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color (hex)</label>
                    <input type="text" id="color" name="color" value="{{ old('color') }}" placeholder="#3B82F6" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="space-y-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300" checked>
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Activo</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_default" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Por defecto</span>
                    </label>
                </div>
                <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Crear estatus</button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Color</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Por Defecto</th>
                        <th class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statuses as $status)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $status->name }}</div>
                                <div class="text-xs text-gray-500">{{ $status->slug }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($status->color)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded" style="background-color: {{ $status->color }}22; color: {{ $status->color }};">
                                        {{ $status->color }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">Sin color</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded {{ $status->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $status->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($status->is_default)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">Sí</span>
                                @else
                                    <span class="text-xs text-gray-500">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 space-y-2">
                                <details>
                                    <summary class="cursor-pointer text-blue-600 hover:text-blue-800 text-xs font-semibold">Editar</summary>
                                    <form method="POST" action="{{ route('admin.order-statuses.update', $status->id) }}" class="mt-2 space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $status->name }}" class="w-full p-2 text-xs border rounded" required>
                                        <input type="text" name="slug" value="{{ $status->slug }}" class="w-full p-2 text-xs border rounded" required>
                                        <input type="text" name="color" value="{{ $status->color }}" class="w-full p-2 text-xs border rounded" placeholder="#3B82F6">
                                        <label class="inline-flex items-center text-xs mr-3">
                                            <input type="checkbox" name="is_active" value="1" {{ $status->is_active ? 'checked' : '' }}>
                                            <span class="ml-1">Activo</span>
                                        </label>
                                        <label class="inline-flex items-center text-xs">
                                            <input type="checkbox" name="is_default" value="1" {{ $status->is_default ? 'checked' : '' }}>
                                            <span class="ml-1">Por defecto</span>
                                        </label>
                                        <button type="submit" class="block text-xs bg-blue-600 text-white px-3 py-1 rounded">Guardar</button>
                                    </form>
                                </details>

                                <div class="flex items-center gap-3">
                                    <form method="POST" action="{{ route('admin.order-statuses.toggle', $status->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-semibold {{ $status->is_active ? 'text-yellow-700' : 'text-green-700' }} hover:underline">
                                            {{ $status->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>

                                    @if(!$status->is_default)
                                        <form method="POST" action="{{ route('admin.order-statuses.default', $status->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs font-semibold text-blue-700 hover:underline">Marcar default</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.order-statuses.destroy', $status->id) }}" onsubmit="return confirm('¿Eliminar este estatus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-700 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500">No hay estatus configurados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $statuses->links() }}
        </div>
    </div>
</div>
@endsection

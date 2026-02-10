@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Editar Sección del Home</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Modifica la configuración de la sección</p>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
    <form action="{{ route('admin.home-sections.update', $section->id) }}" method="POST" id="section-form">
        @csrf
        @method('PUT')
        
        <!-- Tipo de Sección (readonly) -->
        <div class="mb-6">
            <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Tipo de Sección
            </label>
            <input type="text" value="{{ ucfirst(str_replace('_', ' ', $section->type)) }}" readonly
                   class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
            <input type="hidden" name="type" value="{{ $section->type }}">
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">El tipo de sección no se puede cambiar después de crear</p>
        </div>

        <!-- Título -->
        <div class="mb-6">
            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Título <span class="text-red-500">*</span>
            </label>
            <input type="text" id="title" name="title" value="{{ old('title', $section->title) }}" required
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
            @error('title')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Estado -->
        <div class="mb-6">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Sección Activa</span>
            </label>
        </div>

        <!-- Orden de Visualización -->
        <div class="mb-6">
            <label for="display_order" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Orden de Visualización
            </label>
            <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $section->display_order) }}" min="0"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Menor número = aparece primero</p>
            @error('display_order')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Configuración -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Configuración de la Sección</h3>
            <div id="configuration-container">
                @include('admin.home-sections.partials.config-' . $section->type, ['config' => $section->configuration])
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-4">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Actualizar Sección
            </button>
            <a href="{{ route('admin.home-sections.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

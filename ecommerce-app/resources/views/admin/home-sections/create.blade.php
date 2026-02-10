@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Nueva Sección del Home</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Crea una nueva sección para la página principal</p>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
    <form action="{{ route('admin.home-sections.store') }}" method="POST" id="section-form">
        @csrf
        
        <!-- Tipo de Sección -->
        <div class="mb-6">
            <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Tipo de Sección <span class="text-red-500">*</span>
            </label>
            <select id="type" name="type" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                <option value="">Selecciona un tipo</option>
                <option value="hero">Hero - Banner Principal</option>
                <option value="featured_products">Productos Destacados</option>
                <option value="featured_categories">Categorías Destacadas</option>
                <option value="banners">Banners Promocionales</option>
                <option value="testimonials">Testimonios</option>
                <option value="html_block">Bloque HTML Personalizado</option>
            </select>
            @error('type')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Título -->
        <div class="mb-6">
            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Título <span class="text-red-500">*</span>
            </label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                   placeholder="Ej: Hero Principal">
            @error('title')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Estado -->
        <div class="mb-6">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Sección Activa</span>
            </label>
        </div>

        <!-- Orden de Visualización -->
        <div class="mb-6">
            <label for="display_order" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Orden de Visualización
            </label>
            <input type="number" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" min="0"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Menor número = aparece primero</p>
            @error('display_order')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Configuración Dinámica -->
        <div id="configuration-container" class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Configuración de la Sección</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Selecciona un tipo de sección para ver las opciones de configuración</p>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-4">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Crear Sección
            </button>
            <a href="{{ route('admin.home-sections.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                Cancelar
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const configContainer = document.getElementById('configuration-container');
    
    typeSelect.addEventListener('change', function() {
        const type = this.value;
        loadConfigurationForm(type);
    });
    
    function loadConfigurationForm(type) {
        if (!type) {
            configContainer.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">Selecciona un tipo de sección para ver las opciones de configuración</p>';
            return;
        }
        
        let html = '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Configuración de la Sección</h3>';
        
        switch(type) {
            case 'hero':
                html += getHeroForm();
                break;
            case 'featured_products':
                html += getFeaturedProductsForm();
                break;
            case 'featured_categories':
                html += getFeaturedCategoriesForm();
                break;
            case 'banners':
                html += getBannersForm();
                break;
            case 'testimonials':
                html += getTestimonialsForm();
                break;
            case 'html_block':
                html += getHtmlBlockForm();
                break;
        }
        
        configContainer.innerHTML = html;
    }
    
    function getHeroForm() {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título del Hero</label>
                    <input type="text" name="configuration[title]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Bienvenido a Nuestra Tienda">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subtítulo</label>
                    <input type="text" name="configuration[subtitle]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Descubre los mejores productos">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL de Imagen de Fondo</label>
                    <input type="url" name="configuration[background_image]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="https://...">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Opacidad del Overlay (0-1)</label>
                    <input type="number" name="configuration[overlay_opacity]" step="0.1" min="0" max="1" value="0.5" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
            </div>
        `;
    }
    
    function getFeaturedProductsForm() {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título de la Sección</label>
                    <input type="text" name="configuration[heading]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Productos Destacados">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subtítulo</label>
                    <input type="text" name="configuration[subheading]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Los mejores productos para ti">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad de Productos</label>
                    <input type="number" name="configuration[limit]" value="8" min="1" max="20" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Columnas</label>
                    <select name="configuration[columns]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="2">2 columnas</option>
                        <option value="3">3 columnas</option>
                        <option value="4" selected>4 columnas</option>
                        <option value="6">6 columnas</option>
                    </select>
                </div>
                <div class="flex items-center gap-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="configuration[show_price]" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar Precio</span>
                    </label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="configuration[show_rating]" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar Rating</span>
                    </label>
                </div>
            </div>
        `;
    }
    
    function getFeaturedCategoriesForm() {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título de la Sección</label>
                    <input type="text" name="configuration[heading]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Categorías Destacadas">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad de Categorías</label>
                    <input type="number" name="configuration[limit]" value="6" min="1" max="12" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Columnas</label>
                    <select name="configuration[columns]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="2">2 columnas</option>
                        <option value="3" selected>3 columnas</option>
                        <option value="4">4 columnas</option>
                        <option value="6">6 columnas</option>
                    </select>
                </div>
            </div>
        `;
    }
    
    function getBannersForm() {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Diseño</label>
                    <select name="configuration[layout]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="slider" selected>Slider</option>
                        <option value="grid">Grid</option>
                    </select>
                </div>
                <div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="configuration[autoplay]" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Autoplay</span>
                    </label>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Velocidad de Autoplay (ms)</label>
                    <input type="number" name="configuration[autoplay_speed]" value="5000" min="1000" step="1000" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Nota: Los banners se configuran después de crear la sección</p>
            </div>
        `;
    }
    
    function getTestimonialsForm() {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título de la Sección</label>
                    <input type="text" name="configuration[heading]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Lo Que Dicen Nuestros Clientes">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Diseño</label>
                    <select name="configuration[layout]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="carousel" selected>Carousel</option>
                        <option value="grid">Grid</option>
                    </select>
                </div>
                <div class="flex items-center gap-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="configuration[show_rating]" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar Rating</span>
                    </label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="configuration[show_avatar]" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar Avatar</span>
                    </label>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Nota: Los testimonios se configuran después de crear la sección</p>
            </div>
        `;
    }
    
    function getHtmlBlockForm() {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contenido HTML</label>
                    <textarea name="configuration[html_content]" rows="10" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="<div>Tu contenido HTML aquí</div>"></textarea>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Clases CSS Adicionales</label>
                    <input type="text" name="configuration[css_classes]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="my-8 container mx-auto">
                </div>
            </div>
        `;
    }
});
</script>
@endpush
@endsection

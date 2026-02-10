<div class="space-y-4">
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título del Hero</label>
        <input type="text" name="configuration[title]" value="{{ old('configuration.title', $config['title'] ?? '') }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
               placeholder="Bienvenido a Nuestra Tienda">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subtítulo</label>
        <input type="text" name="configuration[subtitle]" value="{{ old('configuration.subtitle', $config['subtitle'] ?? '') }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
               placeholder="Descubre los mejores productos">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL de Imagen de Fondo</label>
        <input type="url" name="configuration[background_image]" value="{{ old('configuration.background_image', $config['background_image'] ?? '') }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
               placeholder="https://...">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Opacidad del Overlay (0-1)</label>
        <input type="number" name="configuration[overlay_opacity]" step="0.1" min="0" max="1" value="{{ old('configuration.overlay_opacity', $config['overlay_opacity'] ?? 0.5) }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
    </div>
    
    <div class="border-t pt-4 mt-4">
        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Botones CTA</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Configura los botones de llamada a la acción (formato JSON)</p>
        <textarea name="configuration[cta_buttons]" rows="6" 
                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono">{{ old('configuration.cta_buttons', json_encode($config['cta_buttons'] ?? [], JSON_PRETTY_PRINT)) }}</textarea>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ejemplo: [{"text":"Comprar","url":"/products","style":"primary"}]</p>
    </div>
</div>

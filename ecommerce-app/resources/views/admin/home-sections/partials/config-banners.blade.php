<div class="space-y-4">
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Diseño</label>
        <select name="configuration[layout]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
            <option value="slider" {{ ($config['layout'] ?? 'slider') == 'slider' ? 'selected' : '' }}>Slider</option>
            <option value="grid" {{ ($config['layout'] ?? 'slider') == 'grid' ? 'selected' : '' }}>Grid</option>
        </select>
    </div>
    <div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="configuration[autoplay]" value="1" {{ old('configuration.autoplay', $config['autoplay'] ?? true) ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Autoplay</span>
        </label>
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Velocidad de Autoplay (ms)</label>
        <input type="number" name="configuration[autoplay_speed]" value="{{ old('configuration.autoplay_speed', $config['autoplay_speed'] ?? 5000) }}" min="1000" step="1000"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
    </div>
    
    <div class="border-t pt-4 mt-4">
        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Banners</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Configura los banners (formato JSON)</p>
        <textarea name="configuration[banners]" rows="10" 
                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono">{{ old('configuration.banners', json_encode($config['banners'] ?? [], JSON_PRETTY_PRINT)) }}</textarea>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ejemplo: [{"image":"url","title":"Título","link":"/ruta"}]</p>
    </div>
</div>

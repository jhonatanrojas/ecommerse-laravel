<div class="space-y-4">
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título de la Sección</label>
        <input type="text" name="configuration[heading]" value="{{ old('configuration.heading', $config['heading'] ?? '') }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
               placeholder="Productos Destacados">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subtítulo</label>
        <input type="text" name="configuration[subheading]" value="{{ old('configuration.subheading', $config['subheading'] ?? '') }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
               placeholder="Los mejores productos para ti">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad de Productos</label>
        <input type="number" name="configuration[limit]" value="{{ old('configuration.limit', $config['limit'] ?? 8) }}" min="1" max="20"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Columnas</label>
        <select name="configuration[columns]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
            <option value="2" {{ ($config['columns'] ?? 4) == 2 ? 'selected' : '' }}>2 columnas</option>
            <option value="3" {{ ($config['columns'] ?? 4) == 3 ? 'selected' : '' }}>3 columnas</option>
            <option value="4" {{ ($config['columns'] ?? 4) == 4 ? 'selected' : '' }}>4 columnas</option>
            <option value="6" {{ ($config['columns'] ?? 4) == 6 ? 'selected' : '' }}>6 columnas</option>
        </select>
    </div>
    <div class="flex items-center gap-4">
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="configuration[show_price]" value="1" {{ old('configuration.show_price', $config['show_price'] ?? true) ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar Precio</span>
        </label>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="configuration[show_rating]" value="1" {{ old('configuration.show_rating', $config['show_rating'] ?? true) ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Mostrar Rating</span>
        </label>
    </div>
</div>

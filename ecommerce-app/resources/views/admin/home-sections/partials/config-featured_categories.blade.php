<div class="space-y-4">
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título de la Sección</label>
        <input type="text" name="configuration[heading]" value="{{ old('configuration.heading', $config['heading'] ?? '') }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
               placeholder="Categorías Destacadas">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad de Categorías</label>
        <input type="number" name="configuration[limit]" value="{{ old('configuration.limit', $config['limit'] ?? 6) }}" min="1" max="12"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Columnas</label>
        <select name="configuration[columns]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
            <option value="2" {{ ($config['columns'] ?? 3) == 2 ? 'selected' : '' }}>2 columnas</option>
            <option value="3" {{ ($config['columns'] ?? 3) == 3 ? 'selected' : '' }}>3 columnas</option>
            <option value="4" {{ ($config['columns'] ?? 3) == 4 ? 'selected' : '' }}>4 columnas</option>
            <option value="6" {{ ($config['columns'] ?? 3) == 6 ? 'selected' : '' }}>6 columnas</option>
        </select>
    </div>
</div>

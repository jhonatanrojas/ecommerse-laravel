<div class="space-y-4">
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contenido HTML</label>
        <textarea name="configuration[html_content]" rows="15" 
                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono" 
                  placeholder="<div>Tu contenido HTML aquí</div>">{{ old('configuration.html_content', $config['html_content'] ?? '') }}</textarea>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Puedes usar HTML y clases de Tailwind CSS</p>
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Clases CSS Adicionales</label>
        <input type="text" name="configuration[css_classes]" value="{{ old('configuration.css_classes', $config['css_classes'] ?? '') }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
               placeholder="my-8 container mx-auto">
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Clases CSS que se aplicarán al contenedor</p>
    </div>
</div>

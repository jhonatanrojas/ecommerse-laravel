<div class="grid gap-4 mb-4 sm:grid-cols-2">
    <!-- Nombre -->
    <div class="sm:col-span-2">
        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Nombre <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror" 
            placeholder="Ingresa el nombre de la categoría" required>
        @error('name')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Slug -->
    <div class="sm:col-span-2">
        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Slug <span class="text-gray-500 text-xs">(Opcional - se genera automáticamente)</span>
        </label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug ?? '') }}" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('slug') border-red-500 @enderror" 
            placeholder="slug-de-la-categoria">
        @error('slug')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Si se deja vacío, se generará automáticamente desde el nombre</p>
    </div>

    <!-- Descripción -->
    <div class="sm:col-span-2">
        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Descripción
        </label>
        <textarea name="description" id="description" rows="4" 
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('description') border-red-500 @enderror" 
            placeholder="Descripción de la categoría">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Orden -->
    <div>
        <label for="order" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Orden
        </label>
        <input type="number" name="order" id="order" value="{{ old('order', $category->order ?? 0) }}" min="0"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('order') border-red-500 @enderror" 
            placeholder="0">
        @error('order')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Orden de visualización (menor número = mayor prioridad)</p>
    </div>

    <!-- Estado -->
    <div class="flex items-center">
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" id="is_active" value="1" 
                class="sr-only peer" 
                {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Categoría activa</span>
        </label>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 ml-3">Las categorías inactivas no se mostrarán en la tienda</p>
    </div>
</div>

<div class="flex items-center space-x-4">
    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        {{ $submitButtonText ?? 'Guardar' }}
    </button>
    <a href="{{ route('admin.categories.index') }}" class="text-gray-600 inline-flex items-center hover:text-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-gray-400 dark:hover:text-white dark:focus:ring-gray-700">
        Cancelar
    </a>
</div>

@push('scripts')
<script>
    // Auto-generar slug desde el nombre
    document.getElementById('name').addEventListener('input', function(e) {
        const slugInput = document.getElementById('slug');
        if (!slugInput.value || slugInput.dataset.autoGenerated) {
            const slug = e.target.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
            slugInput.dataset.autoGenerated = 'true';
        }
    });

    document.getElementById('slug').addEventListener('input', function() {
        if (this.value) {
            delete this.dataset.autoGenerated;
        }
    });
</script>
@endpush

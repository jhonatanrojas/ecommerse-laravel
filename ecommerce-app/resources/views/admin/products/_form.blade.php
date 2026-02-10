<div class="grid gap-4 mb-4 sm:grid-cols-2">
    <!-- Nombre -->
    <div class="sm:col-span-2">
        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Nombre del Producto <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('name') border-red-500 @enderror" 
            placeholder="Ingresa el nombre del producto" required>
        @error('name')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- SKU -->
    <div>
        <label for="sku" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            SKU <span class="text-red-500">*</span>
        </label>
        <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku ?? '') }}" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('sku') border-red-500 @enderror" 
            placeholder="SKU-001" required>
        @error('sku')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Categoría -->
    <div>
        <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Categoría
        </label>
        <select name="category_id" id="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('category_id') border-red-500 @enderror">
            <option value="">Sin categoría</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Precio -->
    <div>
        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Precio <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <span class="text-gray-500 dark:text-gray-400">$</span>
            </div>
            <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full pl-7 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('price') border-red-500 @enderror" 
                placeholder="0.00" required>
        </div>
        @error('price')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Precio de Comparación -->
    <div>
        <label for="compare_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Precio de Comparación
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <span class="text-gray-500 dark:text-gray-400">$</span>
            </div>
            <input type="number" name="compare_price" id="compare_price" step="0.01" min="0" value="{{ old('compare_price', $product->compare_price ?? '') }}" 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full pl-7 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('compare_price') border-red-500 @enderror" 
                placeholder="0.00">
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Precio antes del descuento</p>
        @error('compare_price')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Stock -->
    <div>
        <label for="stock" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Stock <span class="text-red-500">*</span>
        </label>
        <input type="number" name="stock" id="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('stock') border-red-500 @enderror" 
            placeholder="0" required>
        @error('stock')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Umbral de Stock Bajo -->
    <div>
        <label for="low_stock_threshold" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Alerta de Stock Bajo
        </label>
        <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 10) }}" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('low_stock_threshold') border-red-500 @enderror" 
            placeholder="10">
        @error('low_stock_threshold')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Descripción Corta -->
    <div class="sm:col-span-2">
        <label for="short_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Descripción Corta
        </label>
        <textarea name="short_description" id="short_description" rows="2" maxlength="500"
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('short_description') border-red-500 @enderror" 
            placeholder="Descripción breve del producto">{{ old('short_description', $product->short_description ?? '') }}</textarea>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo 500 caracteres</p>
        @error('short_description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Descripción -->
    <div class="sm:col-span-2">
        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Descripción Completa
        </label>
        <textarea name="description" id="description" rows="6" 
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('description') border-red-500 @enderror" 
            placeholder="Descripción detallada del producto">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Imágenes -->
    <div class="sm:col-span-2">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Imágenes del Producto
        </label>
        <input type="file" name="images[]" id="images" multiple accept="image/*" 
            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 @error('images') border-red-500 @enderror">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP (MAX. 2MB cada una, máximo 5 imágenes)</p>
        @error('images')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
        @error('images.*')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Imágenes Existentes (solo en edición) -->
    @if(isset($product) && $product->images->count() > 0)
        <div class="sm:col-span-2">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Imágenes Actuales
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($product->images as $image)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded-lg">
                        @if($image->is_primary)
                            <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">Principal</span>
                        @endif
                        <form action="{{ route('admin.products.images.delete', [$product->id, $image->id]) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('¿Eliminar esta imagen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white p-1 rounded hover:bg-red-700">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Estados -->
    <div class="sm:col-span-2 flex gap-6">
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="sr-only peer" 
                {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Producto activo</span>
        </label>

        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_featured" id="is_featured" value="1" class="sr-only peer" 
                {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 dark:peer-focus:ring-yellow-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-yellow-400"></div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Producto destacado</span>
        </label>
    </div>
</div>

<div class="flex items-center space-x-4 mt-6">
    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        {{ $submitButtonText ?? 'Guardar' }}
    </button>
    <a href="{{ route('admin.products.index') }}" class="text-gray-600 inline-flex items-center hover:text-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-gray-400 dark:hover:text-white dark:focus:ring-gray-700">
        Cancelar
    </a>
</div>

@push('scripts')
<script>
    // Auto-generar slug desde el nombre
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        // Puedes agregar un campo slug oculto si lo necesitas
    });

    // Preview de imágenes
    document.getElementById('images').addEventListener('change', function(e) {
        const files = e.target.files;
        if (files.length > 5) {
            alert('Máximo 5 imágenes permitidas');
            this.value = '';
        }
    });
</script>
@endpush

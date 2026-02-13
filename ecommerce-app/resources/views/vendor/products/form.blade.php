<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="bg-white p-4 rounded border space-y-4">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input name="name" value="{{ old('name', $product->name ?? '') }}" class="border rounded px-3 py-2" placeholder="Nombre" required>
        <input name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="border rounded px-3 py-2" placeholder="SKU" required>
        <select name="category_id" class="border rounded px-3 py-2">
            <option value="">Sin categoría</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? null) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? 0) }}" class="border rounded px-3 py-2" placeholder="Precio" required>
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="border rounded px-3 py-2" placeholder="Stock" required>
        <input type="number" step="0.01" name="weight" value="{{ old('weight', $product->weight ?? '') }}" class="border rounded px-3 py-2" placeholder="Peso">
    </div>

    <textarea name="description" class="w-full border rounded px-3 py-2" placeholder="Descripción">{{ old('description', $product->description ?? '') }}</textarea>

    <div>
        <label class="text-sm">Imágenes</label>
        <input type="file" name="images[]" multiple class="w-full border rounded px-3 py-2">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
        <input name="variants[0][name]" placeholder="Variante 1 nombre" class="border rounded px-3 py-2">
        <input name="variants[0][sku]" placeholder="Variante 1 SKU" class="border rounded px-3 py-2">
        <input name="variants[0][price]" placeholder="Variante 1 precio" class="border rounded px-3 py-2" type="number" step="0.01">
        <input name="variants[0][stock]" placeholder="Variante 1 stock" class="border rounded px-3 py-2" type="number">
    </div>

    @if($vendorProduct)
        <p class="text-sm text-gray-600">Moderación: {{ $vendorProduct->is_approved ? 'Aprobado' : 'Pendiente' }}. Nota: {{ $vendorProduct->moderation_notes ?? 'N/A' }}</p>
    @endif

    <button class="px-4 py-2 bg-black text-white rounded">Guardar</button>
</form>

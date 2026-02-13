@php
    $isEdit = isset($page);
    $titleValue = old('title', $page->title ?? '');
    $slugValue = old('slug', $page->slug ?? '');
    $contentValue = old('content', $page->content ?? '');
    $metaTitleValue = old('meta_title', $page->meta_title ?? '');
    $metaDescriptionValue = old('meta_description', $page->meta_description ?? '');
    $metaKeywordsValue = old('meta_keywords', $page->meta_keywords ?? '');
    $isPublishedValue = old('is_published', $page->is_published ?? false);
@endphp

<div class="grid grid-cols-1 gap-6">
    <div>
        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título</label>
        <input type="text" id="title" name="title" value="{{ $titleValue }}" required
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
               placeholder="Ej. Política de devoluciones">
    </div>

    <div>
        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slug</label>
        <input type="text" id="slug" name="slug" value="{{ $slugValue }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
               placeholder="se-genera-automaticamente-si-lo-dejas-vacio">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Si lo dejas vacío, se generará automáticamente desde el título.</p>
    </div>

    <div>
        <label for="content-editor" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contenido</label>
        <textarea id="content-editor" name="content" rows="18" required
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $contentValue }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="meta_title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" value="{{ $metaTitleValue }}"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                   maxlength="255">
        </div>

        <div>
            <label for="meta_keywords" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Meta Keywords</label>
            <input type="text" id="meta_keywords" name="meta_keywords" value="{{ $metaKeywordsValue }}"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                   placeholder="keyword1, keyword2, keyword3">
        </div>
    </div>

    <div>
        <label for="meta_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Meta Description</label>
        <textarea id="meta_description" name="meta_description" rows="4"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                  maxlength="500">{{ $metaDescriptionValue }}</textarea>
    </div>

    <div class="flex items-center gap-3">
        <input type="hidden" name="is_published" value="0">
        <input id="is_published" name="is_published" type="checkbox" value="1" {{ $isPublishedValue ? 'checked' : '' }}
               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
        <label for="is_published" class="text-sm font-medium text-gray-900 dark:text-gray-300">Publicar página</label>
    </div>
</div>

<div class="mt-8 flex items-center gap-3">
    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
        {{ $submitButtonText ?? ($isEdit ? 'Actualizar Página' : 'Crear Página') }}
    </button>

    <a href="{{ route('admin.pages.index') }}" class="text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-5 py-2.5">
        Cancelar
    </a>
</div>

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof tinymce === 'undefined') {
                return;
            }

            tinymce.init({
                selector: '#content-editor',
                height: 520,
                menubar: 'file edit view insert format tools table help',
                plugins: 'anchor autolink charmap code fullscreen image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat code fullscreen',
                branding: false,
                promotion: false,
                paste_data_images: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                file_picker_callback: function (callback, value, meta) {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.onchange = function () {
                        const file = this.files[0];
                        if (!file) {
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function () {
                            callback(reader.result, { title: file.name });
                        };
                        reader.readAsDataURL(file);
                    };

                    input.click();
                },
            });
        });
    </script>
@endpush

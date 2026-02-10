@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($menu) ? 'Editar Menú' : 'Crear Menú' }}</h1>
        <a href="{{ route('admin.menus.index') }}" class="text-blue-600 hover:underline">Volver a la lista</a>
    </div>

    <!-- Menu Details Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ isset($menu) ? route('admin.menus.update', $menu->uuid) : route('admin.menus.store') }}" method="POST">
            @csrf
            @if(isset($menu))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $menu->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Ubicación</label>
                    <select name="location" id="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="header" {{ (old('location', $menu->location ?? '') == 'header') ? 'selected' : '' }}>Header</option>
                        <option value="footer" {{ (old('location', $menu->location ?? '') == 'footer') ? 'selected' : '' }}>Footer</option>
                        <option value="sidebar" {{ (old('location', $menu->location ?? '') == 'sidebar') ? 'selected' : '' }}>Sidebar</option>
                        <option value="mobile" {{ (old('location', $menu->location ?? '') == 'mobile') ? 'selected' : '' }}>Mobile</option>
                    </select>
                    @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="key" class="block text-sm font-medium text-gray-700">Clave (Única)</label>
                    <input type="text" name="key" id="key" value="{{ old('key', $menu->key ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('key') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4 flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ (old('is_active', $menu->is_active ?? true)) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Activo</label>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    {{ isset($menu) ? 'Actualizar Menú' : 'Crear Menú' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Items Management -->
    @if(isset($menu))
        <div x-data="menuItemsManager" class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Items del Menú</h2>
                <div class="flex gap-2">
                    <button @click="saveOrder" x-bind:disabled="!orderChanged" class="px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Guardar Orden
                    </button>
                    <button @click="openCreateModal" class="px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        Agregar Item
                    </button>
                </div>
            </div>

            <div id="menu-items-list" class="space-y-2 min-h-[50px] border border-dashed border-gray-200 rounded p-4">
                @include('admin.menus.partials.menu-item-row', ['items' => $menu->items])
                
                @if($menu->items->count() === 0)
                    <div class="text-center text-gray-500 py-4">No hay items en este menú.</div>
                @endif
            </div>

            <!-- Modal -->
            @include('admin.menus.partials.item-modal')
        </div>
    @endif
</div>

@if(isset($menu))
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('menuItemsManager', () => ({
            showModal: false,
            editingItem: null,
            orderChanged: false,
            formData: {
                label: '',
                url: '',
                type: 'internal',
                target: '_self',
                icon: '',
                badge_text: '',
                badge_color: '#ff0000',
                is_active: true,
                route_name: '',
                parent_id: null
            },

            init() {
                this.initSortable();
            },

            initSortable() {
                const rootEl = document.getElementById('menu-items-list');
                const nestedEls = document.querySelectorAll('.nested-sortable');
                
                // Root sortable
                new Sortable(rootEl, {
                    group: 'nested',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    handle: '.handle',
                    onEnd: () => { this.orderChanged = true; }
                });

                // Nested sortables
                nestedEls.forEach(el => {
                    new Sortable(el, {
                        group: 'nested',
                        animation: 150,
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        handle: '.handle',
                        onEnd: () => { this.orderChanged = true; }
                    });
                });
            },

            openCreateModal() {
                this.editingItem = null;
                this.resetForm();
                this.showModal = true;
            },

            editItem(item) {
                this.editingItem = item;
                this.formData = {
                    label: item.label,
                    url: item.url,
                    type: item.type,
                    target: item.target,
                    icon: item.icon,
                    badge_text: item.badge_text,
                    badge_color: item.badge_color || '#ff0000',
                    is_active: !!item.is_active,
                    route_name: item.route_name,
                    parent_id: item.parent_id
                };
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
                this.editingItem = null;
                this.resetForm();
            },

            resetForm() {
                this.formData = {
                    label: '',
                    url: '',
                    type: 'internal',
                    target: '_self',
                    icon: '',
                    badge_text: '',
                    badge_color: '#ff0000',
                    is_active: true,
                    route_name: '',
                    parent_id: null
                };
            },

            saveOrder() {
                const items = [];
                const processList = (container, parentId = null, depth = 0) => {
                    const children = Array.from(container.children).filter(el => el.classList.contains('menu-item-container'));
                    children.forEach((el, index) => {
                        const id = el.dataset.id;
                        items.push({
                            id: id,
                            parent_id: parentId,
                            order: index,
                            depth: depth
                        });

                        const nestedContainer = el.querySelector('.nested-sortable');
                        if (nestedContainer) {
                            processList(nestedContainer, id, depth + 1);
                        }
                    });
                };

                processList(document.getElementById('menu-items-list'));

                fetch('{{ route('admin.menu-items.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.orderChanged = false;
                        // Optional: Show success toast
                        alert('Orden guardado correctamente'); 
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar el orden');
                });
            }
        }));
    });
</script>
@endpush
@endif
@endsection

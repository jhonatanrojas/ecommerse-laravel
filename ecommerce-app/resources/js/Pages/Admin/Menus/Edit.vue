<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import NestedDraggable from './Partials/NestedDraggable.vue';
import MenuItemForm from './Partials/MenuItemForm.vue';

const props = defineProps({
    menu: Object,
    items: {
        type: Array,
        default: () => [],
    },
});

const menuForm = useForm({
    key: props.menu ? props.menu.key : '',
    name: props.menu ? props.menu.name : '',
    location: props.menu ? props.menu.location : 'header',
    is_active: props.menu ? !!props.menu.is_active : true,
});

const itemsTree = ref([]);
const showItemModal = ref(false);
const editingItem = ref(null);

// Build tree from flat list
const buildTree = (items, parentId = null) => {
    return items
        .filter(item => item.parent_id === parentId)
        .sort((a, b) => a.order - b.order)
        .map(item => ({
            ...item,
            children: buildTree(items, item.id),
        }));
};

watch(() => props.items, (newItems) => {
    itemsTree.value = buildTree(newItems);
}, { immediate: true });

const submitMenu = () => {
    if (props.menu) {
        menuForm.put(route('admin.menus.update', props.menu.uuid));
    } else {
        menuForm.post(route('admin.menus.store'));
    }
};

const openCreateModal = () => {
    editingItem.value = null;
    showItemModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    showItemModal.value = true;
};

const closeItemModal = () => {
    showItemModal.value = false;
    editingItem.value = null;
};

const deleteItem = (item) => {
    if (confirm('Are you sure you want to delete this item?')) {
        router.delete(route('admin.menu-items.destroy', item.uuid), {
            preserveScroll: true,
        });
    }
};

const onItemSaved = () => {
    closeItemModal();
    // Refresh items
    router.reload({ only: ['items'] });
};

// Flatten tree to get order and parent_id
const flattenTree = (tree, parentId = null, depth = 0) => {
    let result = [];
    tree.forEach((node, index) => {
        result.push({
            id: node.id,
            parent_id: parentId,
            order: index + 1,
            depth: depth,
        });
        if (node.children && node.children.length > 0) {
            result = result.concat(flattenTree(node.children, node.id, depth + 1));
        }
    });
    return result;
};

const saveOrder = () => {
    const flatItems = flattenTree(itemsTree.value);
    router.post(route('admin.menu-items.reorder'), {
        items: flatItems,
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="menu ? 'Editar Menú' : 'Crear Menú'" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Menu Details Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                     <h3 class="text-lg font-medium text-gray-900">Detalles del Menú</h3>
                     <Link :href="route('admin.menus.index')" class="text-indigo-600 hover:text-indigo-900 text-sm">Volver a la lista</Link>
                </div>
                
                <form @submit.prevent="submitMenu">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                        <!-- Key -->
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium leading-6 text-gray-900">Clave (Única)</label>
                            <div class="mt-2">
                                <input v-model="menuForm.key" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div v-if="menuForm.errors.key" class="text-red-600 text-xs mt-1">{{ menuForm.errors.key }}</div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium leading-6 text-gray-900">Nombre</label>
                            <div class="mt-2">
                                <input v-model="menuForm.name" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div v-if="menuForm.errors.name" class="text-red-600 text-xs mt-1">{{ menuForm.errors.name }}</div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium leading-6 text-gray-900">Ubicación</label>
                            <div class="mt-2">
                                <select v-model="menuForm.location" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="header">Header</option>
                                    <option value="footer">Footer</option>
                                    <option value="sidebar">Sidebar</option>
                                    <option value="mobile">Mobile</option>
                                </select>
                                <div v-if="menuForm.errors.location" class="text-red-600 text-xs mt-1">{{ menuForm.errors.location }}</div>
                            </div>
                        </div>

                        <!-- Active -->
                        <div class="sm:col-span-3 flex items-center mt-8">
                             <div class="flex h-6 items-center">
                                <input v-model="menuForm.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                             </div>
                             <div class="ml-3 text-sm leading-6">
                                <label class="font-medium text-gray-900">Activo</label>
                             </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            {{ menu ? 'Actualizar Menú' : 'Crear Menú' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Items Management (Only if editing) -->
            <div v-if="menu" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                     <h3 class="text-lg font-medium text-gray-900">Items del Menú</h3>
                     <div class="flex space-x-2">
                         <button @click="saveOrder" class="px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">Guardar Orden</button>
                         <button @click="openCreateModal" class="px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Agregar Item</button>
                     </div>
                </div>

                <div class="border rounded-md p-4 bg-gray-50 min-h-[100px]">
                    <NestedDraggable 
                        v-model="itemsTree" 
                        @edit="openEditModal" 
                        @delete="deleteItem" 
                    />
                    <div v-if="itemsTree.length === 0" class="text-center text-gray-400 py-8">
                        No hay items. Arrastra items aquí o agrega uno nuevo.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <MenuItemForm 
        :show="showItemModal" 
        :item="editingItem" 
        :menu-id="menu?.id" 
        @close="closeItemModal" 
        @save="onItemSaved" 
    />
</template>

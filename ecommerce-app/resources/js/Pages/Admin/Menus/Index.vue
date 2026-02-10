<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    menus: Array,
});

const form = useForm({});

const deleteMenu = (uuid) => {
    if (confirm('Are you sure you want to delete this menu?')) {
        form.delete(route('admin.menus.destroy', uuid));
    }
};

const toggleMenu = (uuid) => {
    form.post(route('admin.menus.toggle', uuid));
};

const clearCache = () => {
    form.post(route('admin.menus.clear-cache'));
};
</script>

<template>
    <Head title="Menus" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Menús</h2>
                    <div class="flex space-x-2">
                        <button @click="clearCache" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                            Limpiar Cache
                        </button>
                        <Link :href="route('admin.menus.create')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Crear Menú
                        </Link>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clave</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="menu in menus" :key="menu.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ menu.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap capitalize">{{ menu.location }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ menu.key }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button @click="toggleMenu(menu.uuid)" :class="menu.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer">
                                        {{ menu.is_active ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('admin.menus.edit', menu.uuid)" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</Link>
                                    <button @click="deleteMenu(menu.uuid)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                            <tr v-if="menus.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay menús registrados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

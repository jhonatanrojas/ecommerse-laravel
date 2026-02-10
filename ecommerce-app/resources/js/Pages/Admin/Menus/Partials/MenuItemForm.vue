<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

const props = defineProps({
    show: Boolean,
    item: Object,
    menuId: [Number, String],
    parentId: [Number, String, null],
});

const emit = defineEmits(['close', 'save']);

const form = useForm({
    id: null,
    menu_id: props.menuId,
    parent_id: props.parentId,
    label: '',
    url: '',
    type: 'internal',
    target: '_self',
    icon: '',
    css_classes: '',
    badge_text: '',
    badge_color: '',
    is_active: true,
    is_featured: false, // Make sure backend supports this if requested, migration has it.
    open_in_new_tab: false,
    route_name: '',
    route_params: [], // JSON/Array logic might need handling
});

watch(() => props.item, (newItem) => {
    if (newItem) {
        form.id = newItem.id;
        form.menu_id = newItem.menu_id;
        form.parent_id = newItem.parent_id;
        form.label = newItem.label;
        form.url = newItem.url;
        form.type = newItem.type;
        form.target = newItem.target;
        form.icon = newItem.icon;
        form.css_classes = newItem.css_classes;
        form.badge_text = newItem.badge_text;
        form.badge_color = newItem.badge_color;
        form.is_active = !!newItem.is_active;
        form.is_featured = !!newItem.is_featured;
        form.open_in_new_tab = !!newItem.open_in_new_tab;
        form.route_name = newItem.route_name;
        // Handle route_params if needed
    } else {
        form.reset();
        form.menu_id = props.menuId;
        form.parent_id = props.parentId;
    }
}, { immediate: true });

const submit = () => {
    if (form.id) {
        // Update
        // Logic should be handled by parent or here?
        // Usually form.put(route(...))
        // But using UUID or ID?
        // Controller uses UUID. Frontend item usually has UUID.
        // Assuming item has uuid.
        form.put(route('admin.menu-items.update', props.item.uuid), {
            onSuccess: () => emit('save'),
        });
    } else {
        // Create
        form.post(route('admin.menu-items.store'), {
            onSuccess: () => emit('save'),
        });
    }
};

const close = () => {
    emit('close');
};
</script>

<template>
    <TransitionRoot as="template" :show="show">
        <Dialog as="div" class="relative z-10" @close="close">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <form @submit.prevent="submit">
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                            <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">{{ item ? 'Editar Item' : 'Nuevo Item' }}</DialogTitle>
                                            <div class="mt-2 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                                                
                                                <!-- Label -->
                                                <div class="sm:col-span-6">
                                                    <label class="block text-sm font-medium leading-6 text-gray-900">Etiqueta</label>
                                                    <div class="mt-2">
                                                        <input v-model="form.label" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                    </div>
                                                </div>

                                                <!-- Type -->
                                                <div class="sm:col-span-3">
                                                    <label class="block text-sm font-medium leading-6 text-gray-900">Tipo</label>
                                                    <div class="mt-2">
                                                        <select v-model="form.type" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option value="internal">Interno</option>
                                                            <option value="external">Externo</option>
                                                            <option value="route">Ruta Laravel</option>
                                                            <option value="custom">Personalizado</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- URL -->
                                                <div class="sm:col-span-6" v-if="form.type !== 'route'">
                                                    <label class="block text-sm font-medium leading-6 text-gray-900">URL</label>
                                                    <div class="mt-2">
                                                        <input v-model="form.url" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                    </div>
                                                </div>

                                                <!-- Route Name -->
                                                <div class="sm:col-span-6" v-if="form.type === 'route'">
                                                    <label class="block text-sm font-medium leading-6 text-gray-900">Nombre de Ruta</label>
                                                    <div class="mt-2">
                                                        <input v-model="form.route_name" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                    </div>
                                                </div>

                                                <!-- Target -->
                                                <div class="sm:col-span-3">
                                                    <label class="block text-sm font-medium leading-6 text-gray-900">Destino</label>
                                                    <div class="mt-2">
                                                        <select v-model="form.target" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option value="_self">Misma ventana</option>
                                                            <option value="_blank">Nueva ventana</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <!-- Icon -->
                                                <div class="sm:col-span-3">
                                                    <label class="block text-sm font-medium leading-6 text-gray-900">Icono (Heroicons)</label>
                                                    <div class="mt-2">
                                                        <input v-model="form.icon" type="text" placeholder="heroicon-home" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                    </div>
                                                </div>

                                                <!-- Toggles -->
                                                <div class="sm:col-span-6 flex gap-4">
                                                    <div class="relative flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900">Activo</label>
                                                        </div>
                                                    </div>
                                                    <div class="relative flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input v-model="form.is_featured" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900">Destacado</label>
                                                        </div>
                                                    </div>
                                                    <div class="relative flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input v-model="form.open_in_new_tab" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900">Abrir en nueva pestaña</label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Guardar</button>
                                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="close">Cancelar</button>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

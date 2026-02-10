<div x-show="showModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <form :action="editingItem ? '{{ url('admin/menu-items') }}/' + editingItem.uuid : '{{ route('admin.menu-items.store') }}'" method="POST">
                    @csrf
                    <template x-if="editingItem">
                        @method('PUT')
                    </template>
                    
                    <input type="hidden" name="menu_id" value="{{ $menu->id ?? '' }}">
                    <input type="hidden" name="parent_id" x-model="formData.parent_id">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title" x-text="editingItem ? 'Editar Item' : 'Nuevo Item'"></h3>
                                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                                    
                                    <!-- Label -->
                                    <div class="sm:col-span-6">
                                        <label for="label" class="block text-sm font-medium leading-6 text-gray-900">Etiqueta</label>
                                        <div class="mt-2">
                                            <input type="text" name="label" id="label" x-model="formData.label" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>

                                    <!-- Type -->
                                    <div class="sm:col-span-3">
                                        <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Tipo</label>
                                        <div class="mt-2">
                                            <select id="type" name="type" x-model="formData.type" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                                <option value="internal">Interno</option>
                                                <option value="external">Externo</option>
                                                <option value="route">Ruta Laravel</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- URL -->
                                    <div class="sm:col-span-6" x-show="formData.type !== 'route'">
                                        <label for="url" class="block text-sm font-medium leading-6 text-gray-900">URL</label>
                                        <div class="mt-2">
                                            <input type="text" name="url" id="url" x-model="formData.url" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>

                                    <!-- Route Name -->
                                    <div class="sm:col-span-6" x-show="formData.type === 'route'">
                                        <label for="route_name" class="block text-sm font-medium leading-6 text-gray-900">Nombre de Ruta</label>
                                        <div class="mt-2">
                                            <input type="text" name="route_name" id="route_name" x-model="formData.route_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>

                                    <!-- Target -->
                                    <div class="sm:col-span-3">
                                        <label for="target" class="block text-sm font-medium leading-6 text-gray-900">Destino</label>
                                        <div class="mt-2">
                                            <select id="target" name="target" x-model="formData.target" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                                <option value="_self">Misma ventana</option>
                                                <option value="_blank">Nueva ventana</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Icon -->
                                    <div class="sm:col-span-3">
                                        <label for="icon" class="block text-sm font-medium leading-6 text-gray-900">Icono (Clase)</label>
                                        <div class="mt-2">
                                            <input type="text" name="icon" id="icon" x-model="formData.icon" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>

                                    <!-- Badge -->
                                    <div class="sm:col-span-3">
                                        <label for="badge_text" class="block text-sm font-medium leading-6 text-gray-900">Texto Badge</label>
                                        <div class="mt-2">
                                            <input type="text" name="badge_text" id="badge_text" x-model="formData.badge_text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="badge_color" class="block text-sm font-medium leading-6 text-gray-900">Color Badge</label>
                                        <div class="mt-2">
                                            <input type="color" name="badge_color" id="badge_color" x-model="formData.badge_color" class="block w-full h-9 rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>
                                    
                                     <div class="sm:col-span-6 flex gap-4">
                                        <div class="relative flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input id="is_active" name="is_active" type="checkbox" value="1" x-model="formData.is_active" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label for="is_active" class="font-medium text-gray-900">Activo</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Guardar</button>
                        <button type="button" @click="closeModal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

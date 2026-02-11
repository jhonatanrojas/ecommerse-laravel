@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Ajustes Generales de la Tienda</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configura los ajustes globales de tu tienda</p>
</div>

<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
    <form action="{{ route('admin.settings.store.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            
            <!-- Nombre de la Tienda -->
            <div class="lg:col-span-2">
                <label for="store_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nombre de la Tienda <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="store_name" 
                    name="store_name" 
                    value="{{ old('store_name', $settings->store_name ?? '') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('store_name') border-red-500 @enderror"
                    placeholder="Mi Tienda Online"
                    required
                >
                @error('store_name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Logo -->
            <div class="lg:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Logo de la Tienda
                </label>
                
                @if($settings && $settings->logo)
                    <div class="mb-3">
                        <img src="{{ $settings->logo_url }}" alt="Logo actual" class="h-20 w-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Logo actual</p>
                    </div>
                @endif

                <div class="flex items-center justify-center w-full">
                    <label for="logo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click para subir</span> o arrastra y suelta</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, SVG (MAX. 2MB)</p>
                        </div>
                        <input id="logo" name="logo" type="file" class="hidden" accept="image/*" />
                    </label>
                </div>
                @error('logo')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Moneda Principal -->
            <div>
                <label for="currency" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Moneda Principal <span class="text-red-500">*</span>
                </label>
                <select 
                    id="currency" 
                    name="currency"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('currency') border-red-500 @enderror"
                    required
                >
                    <option value="USD" {{ old('currency', $settings->currency ?? 'USD') == 'USD' ? 'selected' : '' }}>USD - Dólar Estadounidense</option>
                    <option value="EUR" {{ old('currency', $settings->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                    <option value="VES" {{ old('currency', $settings->currency ?? '') == 'VES' ? 'selected' : '' }}>VES - Bolívar Venezolano</option>
                    <option value="MXN" {{ old('currency', $settings->currency ?? '') == 'MXN' ? 'selected' : '' }}>MXN - Peso Mexicano</option>
                    <option value="COP" {{ old('currency', $settings->currency ?? '') == 'COP' ? 'selected' : '' }}>COP - Peso Colombiano</option>
                    <option value="ARS" {{ old('currency', $settings->currency ?? '') == 'ARS' ? 'selected' : '' }}>ARS - Peso Argentino</option>
                </select>
                @error('currency')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Símbolo de Moneda -->
            <div>
                <label for="currency_symbol" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Símbolo de Moneda <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="currency_symbol" 
                    name="currency_symbol" 
                    value="{{ old('currency_symbol', $settings->currency_symbol ?? '$') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('currency_symbol') border-red-500 @enderror"
                    placeholder="$"
                    required
                >
                @error('currency_symbol')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tasa de Impuesto -->
            <div>
                <label for="tax_rate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Tasa de Impuesto (%) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="tax_rate" 
                    name="tax_rate" 
                    value="{{ old('tax_rate', $settings->tax_rate ?? '0.00') }}"
                    step="0.01"
                    min="0"
                    max="100"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('tax_rate') border-red-500 @enderror"
                    placeholder="0.00"
                    required
                >
                @error('tax_rate')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email de Soporte -->
            <div>
                <label for="support_email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Email de Soporte
                </label>
                <input 
                    type="email" 
                    id="support_email" 
                    name="support_email" 
                    value="{{ old('support_email', $settings->support_email ?? '') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('support_email') border-red-500 @enderror"
                    placeholder="soporte@mitienda.com"
                >
                @error('support_email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Transaccional -->
            <div>
                <label for="transactional_email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Email Transaccional
                </label>
                <input 
                    type="email" 
                    id="transactional_email" 
                    name="transactional_email" 
                    value="{{ old('transactional_email', $settings->transactional_email ?? '') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('transactional_email') border-red-500 @enderror"
                    placeholder="noreply@mitienda.com"
                >
                @error('transactional_email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Modo de Mantenimiento -->
            <div class="lg:col-span-2">
                <div class="flex items-center">
                    <input 
                        id="maintenance_mode" 
                        name="maintenance_mode" 
                        type="checkbox" 
                        value="1"
                        {{ old('maintenance_mode', $settings->maintenance_mode ?? false) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <label for="maintenance_mode" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Activar Modo de Mantenimiento
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Cuando está activado, la tienda mostrará una página de mantenimiento a los visitantes
                </p>
                @error('maintenance_mode')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Permitir Checkout de Invitados -->
            <div class="lg:col-span-2">
                <div class="flex items-center">
                    <input 
                        id="allow_guest_checkout" 
                        name="allow_guest_checkout" 
                        type="checkbox" 
                        value="1"
                        {{ old('allow_guest_checkout', $settings->allow_guest_checkout ?? false) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <label for="allow_guest_checkout" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Permitir Checkout de Invitados
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Cuando está activado, los usuarios pueden completar compras sin necesidad de crear una cuenta o iniciar sesión
                </p>
                @error('allow_guest_checkout')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end mt-6 space-x-3">
            <a 
                href="{{ route('dashboard') }}" 
                class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
            >
                Cancelar
            </a>
            <button 
                type="submit" 
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
            >
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Preview de imagen antes de subir
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Aquí podrías agregar una vista previa si lo deseas
                console.log('Archivo seleccionado:', file.name);
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection

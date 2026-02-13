@php
    $method = $paymentMethod ?? null;
@endphp

<div class="grid gap-4 mb-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Nombre <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            id="name"
            value="{{ old('name', $method->name ?? '') }}"
            class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5 @error('name') border-red-500 @enderror"
            placeholder="Ej: Stripe"
            required
        >
    </div>

    <div>
        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Slug <span class="text-gray-500 text-xs">(opcional)</span>
        </label>
        <input
            type="text"
            name="slug"
            id="slug"
            value="{{ old('slug', $method->slug ?? '') }}"
            class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5 @error('slug') border-red-500 @enderror"
            placeholder="stripe"
        >
    </div>

    <div>
        <label for="driver_class" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Clase Driver <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="driver_class"
            id="driver_class"
            value="{{ old('driver_class', $method->driver_class ?? '') }}"
            class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5 @error('driver_class') border-red-500 @enderror"
            placeholder="App\Services\Payments\Drivers\StripePaymentDriver"
            required
        >
    </div>

    <div class="sm:col-span-2">
        <label class="relative inline-flex items-center cursor-pointer">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="sr-only peer"
                {{ old('is_active', $method->is_active ?? true) ? 'checked' : '' }}
            >
            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Método activo</span>
        </label>
    </div>

    <div class="sm:col-span-2">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Configuración (JSON)
        </label>
        <div id="dynamic-config-fields" class="mb-3 grid gap-3 sm:grid-cols-2"></div>
        <textarea
            name="config_json"
            id="config_json"
            rows="8"
            class="block p-2.5 w-full text-sm bg-gray-50 rounded-lg border border-gray-300 @error('config') border-red-500 @enderror"
            placeholder='{"api_key":"", "secret_key":"", "mode":"sandbox"}'
        >{{ old('config_json', json_encode($method->config ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">Guarda aquí API keys, tokens, modo sandbox/live y demás credenciales.</p>
    </div>
</div>

<div class="flex items-center space-x-4">
    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
        {{ $submitButtonText ?? 'Guardar' }}
    </button>
    <a href="{{ route('admin.payment-methods.index') }}" class="text-gray-600 hover:text-gray-900 font-medium rounded-lg text-sm px-5 py-2.5">
        Cancelar
    </a>
</div>

@push('scripts')
<script>
    const configSchemas = {
        stripe: ['mode', 'public_key', 'secret_key', 'webhook_secret'],
        paypal: ['mode', 'client_id', 'client_secret', 'webhook_secret'],
        mercadopago: ['mode', 'access_token', 'public_key', 'webhook_secret'],
        cash: ['auto_approve', 'instructions'],
    };

    const slugInput = document.getElementById('slug');
    const configTextarea = document.getElementById('config_json');
    const dynamicContainer = document.getElementById('dynamic-config-fields');

    const safeParseConfig = () => {
        try {
            const parsed = JSON.parse(configTextarea.value || '{}');
            return typeof parsed === 'object' && parsed !== null ? parsed : {};
        } catch (e) {
            return {};
        }
    };

    const writeConfig = (config) => {
        configTextarea.value = JSON.stringify(config, null, 2);
    };

    const renderDynamicFields = () => {
        if (!dynamicContainer) return;

        const slug = (slugInput?.value || '').toLowerCase().replace(/_/g, '');
        const schema = configSchemas[slug] || [];
        const config = safeParseConfig();

        dynamicContainer.innerHTML = '';
        if (schema.length === 0) return;

        schema.forEach((key) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'sm:col-span-1';

            const label = document.createElement('label');
            label.className = 'block mb-1 text-xs font-medium text-gray-700';
            label.textContent = key.replace(/_/g, ' ');

            const input = document.createElement('input');
            input.type = key.includes('secret') || key.includes('token') ? 'password' : 'text';
            input.className = 'bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5';
            input.value = config[key] ?? '';

            if (key === 'auto_approve') {
                input.type = 'text';
                input.placeholder = 'true o false';
            }

            input.addEventListener('input', () => {
                const next = safeParseConfig();
                if (key === 'auto_approve') {
                    next[key] = input.value === 'true';
                } else {
                    next[key] = input.value;
                }
                writeConfig(next);
            });

            wrapper.appendChild(label);
            wrapper.appendChild(input);
            dynamicContainer.appendChild(wrapper);
        });
    };

    document.getElementById('name')?.addEventListener('input', function(e) {
        if (!slugInput.value || slugInput.dataset.autoGenerated) {
            const slug = e.target.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
            slugInput.dataset.autoGenerated = 'true';
            renderDynamicFields();
        }
    });

    slugInput?.addEventListener('input', renderDynamicFields);
    configTextarea?.addEventListener('input', renderDynamicFields);
    renderDynamicFields();
</script>
@endpush

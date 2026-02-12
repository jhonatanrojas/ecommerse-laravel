<template>
  <form class="space-y-4" @submit.prevent="submit">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <InputText
        name="first_name"
        label="Nombre"
        v-model="form.first_name"
        :error="fieldError('first_name') || localErrors.first_name"
        autocomplete="given-name"
        required
      />

      <InputText
        name="last_name"
        label="Apellido"
        v-model="form.last_name"
        :error="fieldError('last_name') || localErrors.last_name"
        autocomplete="family-name"
        required
      />
    </div>

    <InputText
      name="phone"
      label="Teléfono"
      type="tel"
      v-model="form.phone"
      :error="fieldError('phone') || localErrors.phone"
      autocomplete="tel"
      required
    />

    <InputText
      name="address_line_1"
      label="Dirección línea 1"
      v-model="form.address_line_1"
      :error="fieldError('address_line_1') || localErrors.address_line_1"
      autocomplete="address-line1"
      placeholder="Calle, número, etc."
      required
    />

    <InputText
      name="address_line_2"
      label="Dirección línea 2 (opcional)"
      v-model="form.address_line_2"
      :error="fieldError('address_line_2')"
      autocomplete="address-line2"
      placeholder="Apartamento, piso, etc."
    />

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <InputText
        name="city"
        label="Ciudad"
        v-model="form.city"
        :error="fieldError('city') || localErrors.city"
        autocomplete="address-level2"
        required
      />

      <InputText
        name="state"
        label="Estado/Provincia"
        v-model="form.state"
        :error="fieldError('state') || localErrors.state"
        autocomplete="address-level1"
        required
      />

      <InputText
        name="postal_code"
        label="Código postal"
        v-model="form.postal_code"
        :error="fieldError('postal_code') || localErrors.postal_code"
        autocomplete="postal-code"
        required
      />
    </div>

    <InputText
      name="country"
      label="País"
      v-model="form.country"
      :error="fieldError('country') || localErrors.country"
      autocomplete="country-name"
      required
    />

    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700">
        Tipo de dirección
      </label>
      <div class="flex gap-4">
        <label class="flex items-center cursor-pointer">
          <input
            type="radio"
            name="type"
            value="shipping"
            v-model="form.type"
            class="w-4 h-4 text-blue-600 focus:ring-blue-500"
          />
          <span class="ml-2 text-sm text-gray-700">Envío</span>
        </label>
        <label class="flex items-center cursor-pointer">
          <input
            type="radio"
            name="type"
            value="billing"
            v-model="form.type"
            class="w-4 h-4 text-blue-600 focus:ring-blue-500"
          />
          <span class="ml-2 text-sm text-gray-700">Facturación</span>
        </label>
      </div>
    </div>

    <label class="flex items-center cursor-pointer">
      <input
        type="checkbox"
        v-model="form.is_default"
        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
      />
      <span class="ml-2 text-sm text-gray-700">Establecer como dirección por defecto</span>
    </label>

    <div class="flex gap-3 pt-2">
      <SubmitButton
        text="Guardar dirección"
        loading-text="Guardando..."
        :loading="loading"
        :disabled="loading"
        variant="primary"
      />
      <button
        type="button"
        @click="$emit('cancel')"
        class="flex-1 px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200"
        :disabled="loading"
      >
        Cancelar
      </button>
    </div>
  </form>
</template>

<script setup>
import { reactive, watch } from 'vue';
import InputText from '../forms/InputText.vue';
import SubmitButton from '../forms/SubmitButton.vue';

const props = defineProps({
  address: { type: Object, default: null },
  defaultType: { type: String, default: 'shipping' },
  loading: { type: Boolean, default: false },
  fieldErrors: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['submit', 'cancel']);

const form = reactive({
  first_name: '',
  last_name: '',
  phone: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  type: props.defaultType,
  is_default: false,
});

const localErrors = reactive({
  first_name: '',
  last_name: '',
  phone: '',
  address_line_1: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
});

// Cargar datos si estamos editando
watch(() => props.address, (newAddress) => {
  if (newAddress) {
    form.first_name = newAddress.first_name || '';
    form.last_name = newAddress.last_name || '';
    form.phone = newAddress.phone || '';
    form.address_line_1 = newAddress.address_line_1 || '';
    form.address_line_2 = newAddress.address_line_2 || '';
    form.city = newAddress.city || '';
    form.state = newAddress.state || '';
    form.postal_code = newAddress.postal_code || '';
    form.country = newAddress.country || '';
    form.type = newAddress.type || props.defaultType;
    form.is_default = newAddress.is_default || false;
  }
}, { immediate: true });

const fieldError = (field) => props.fieldErrors?.[field]?.[0] || '';

const validate = () => {
  // Limpiar errores
  Object.keys(localErrors).forEach(key => localErrors[key] = '');

  if (!form.first_name.trim()) localErrors.first_name = 'El nombre es requerido.';
  if (!form.last_name.trim()) localErrors.last_name = 'El apellido es requerido.';
  if (!form.phone.trim()) localErrors.phone = 'El teléfono es requerido.';
  if (!form.address_line_1.trim()) localErrors.address_line_1 = 'La dirección es requerida.';
  if (!form.city.trim()) localErrors.city = 'La ciudad es requerida.';
  if (!form.state.trim()) localErrors.state = 'El estado/provincia es requerido.';
  if (!form.postal_code.trim()) localErrors.postal_code = 'El código postal es requerido.';
  if (!form.country.trim()) localErrors.country = 'El país es requerido.';

  return Object.values(localErrors).every(error => !error);
};

const submit = () => {
  if (!validate()) return;

  emit('submit', {
    first_name: form.first_name.trim(),
    last_name: form.last_name.trim(),
    phone: form.phone.trim(),
    address_line_1: form.address_line_1.trim(),
    address_line_2: form.address_line_2.trim(),
    city: form.city.trim(),
    state: form.state.trim(),
    postal_code: form.postal_code.trim(),
    country: form.country.trim(),
    type: form.type,
    is_default: form.is_default,
  });
};
</script>

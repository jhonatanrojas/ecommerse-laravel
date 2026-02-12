<template>
  <div class="space-y-4">
    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5">
      <input
        id="useSameAddress"
        v-model="useSameAddress"
        type="checkbox"
        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
        @change="handleSameAddressToggle"
      />
      <span class="text-sm font-medium text-gray-700">Usar la misma dirección de envío</span>
    </label>

    <div v-if="storeErrors.length && !useSameAddress" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
      <p v-for="error in storeErrors" :key="error">{{ error }}</p>
    </div>

    <div v-if="!useSameAddress" class="space-y-4">
      <CheckoutInput
        id="billing-fullName"
        v-model="localAddress.fullName"
        label="Nombre completo"
        placeholder="Juan Pérez"
        required
        :error="fieldErrors.fullName"
        autocomplete="name"
        @blur="validateField('fullName')"
      />

      <CheckoutInput
        id="billing-addressLine1"
        v-model="localAddress.addressLine1"
        label="Dirección"
        placeholder="Calle Principal 123"
        required
        :error="fieldErrors.addressLine1"
        autocomplete="address-line1"
        @blur="validateField('addressLine1')"
      />

      <CheckoutInput
        id="billing-addressLine2"
        v-model="localAddress.addressLine2"
        label="Dirección línea 2"
        placeholder="Apartamento, suite, etc."
        autocomplete="address-line2"
      />

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <CheckoutInput
          id="billing-city"
          v-model="localAddress.city"
          label="Ciudad"
          placeholder="Madrid"
          required
          :error="fieldErrors.city"
          autocomplete="address-level2"
          @blur="validateField('city')"
        />

        <CheckoutInput
          id="billing-state"
          v-model="localAddress.state"
          label="Estado / Provincia"
          placeholder="Madrid"
          required
          :error="fieldErrors.state"
          autocomplete="address-level1"
          @blur="validateField('state')"
        />
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <CheckoutInput
          id="billing-postalCode"
          v-model="localAddress.postalCode"
          label="Código postal"
          placeholder="28001"
          required
          :error="fieldErrors.postalCode"
          autocomplete="postal-code"
          @blur="validateField('postalCode')"
        />

        <CheckoutInput
          id="billing-country"
          v-model="localAddress.country"
          type="select"
          label="País"
          placeholder="Seleccionar país"
          required
          :options="countryOptions"
          :error="fieldErrors.country"
          autocomplete="country"
          @blur="validateField('country')"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { useCheckoutStore } from '../../stores/checkout';
import CheckoutInput from './CheckoutInput.vue';

const checkoutStore = useCheckoutStore();

const countryOptions = [
  { value: 'ES', label: 'España' },
  { value: 'MX', label: 'México' },
  { value: 'AR', label: 'Argentina' },
  { value: 'CO', label: 'Colombia' },
  { value: 'CL', label: 'Chile' },
  { value: 'PE', label: 'Perú' },
  { value: 'VE', label: 'Venezuela' },
  { value: 'US', label: 'Estados Unidos' },
];

const useSameAddress = ref(checkoutStore.useSameAddress);

const localAddress = reactive({
  fullName: checkoutStore.billingAddress.fullName,
  addressLine1: checkoutStore.billingAddress.addressLine1,
  addressLine2: checkoutStore.billingAddress.addressLine2,
  city: checkoutStore.billingAddress.city,
  state: checkoutStore.billingAddress.state,
  postalCode: checkoutStore.billingAddress.postalCode,
  country: checkoutStore.billingAddress.country,
});

const fieldErrors = reactive({
  fullName: '',
  addressLine1: '',
  city: '',
  state: '',
  postalCode: '',
  country: '',
});

const storeErrors = computed(() => checkoutStore.errors.billing_address || []);

const handleSameAddressToggle = () => {
  checkoutStore.toggleSameAddress(useSameAddress.value);

  if (useSameAddress.value) {
    Object.assign(localAddress, checkoutStore.shippingAddress);
  }
};

const validateField = (fieldName) => {
  fieldErrors[fieldName] = '';

  switch (fieldName) {
    case 'fullName':
      if (!localAddress.fullName) {
        fieldErrors.fullName = 'El nombre completo es requerido';
      } else if (localAddress.fullName.length < 3) {
        fieldErrors.fullName = 'El nombre debe tener al menos 3 caracteres';
      }
      break;
    case 'addressLine1':
      if (!localAddress.addressLine1) {
        fieldErrors.addressLine1 = 'La dirección es requerida';
      }
      break;
    case 'city':
      if (!localAddress.city) {
        fieldErrors.city = 'La ciudad es requerida';
      }
      break;
    case 'state':
      if (!localAddress.state) {
        fieldErrors.state = 'El estado/provincia es requerido';
      }
      break;
    case 'postalCode':
      if (!localAddress.postalCode) {
        fieldErrors.postalCode = 'El código postal es requerido';
      }
      break;
    case 'country':
      if (!localAddress.country) {
        fieldErrors.country = 'El país es requerido';
      }
      break;
  }
};

watch(localAddress, (newAddress) => {
  if (!useSameAddress.value) {
    checkoutStore.setBillingAddress(newAddress);
  }
}, { deep: true });
</script>

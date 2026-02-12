<template>
  <div class="space-y-4">
    <div v-if="storeErrors.length" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
      <p v-for="error in storeErrors" :key="error">{{ error }}</p>
    </div>

    <CheckoutInput
      id="shipping-fullName"
      v-model="localAddress.fullName"
      label="Nombre completo"
      placeholder="Juan Pérez"
      required
      :error="fieldErrors.fullName"
      autocomplete="name"
      @blur="validateField('fullName')"
    />

    <CheckoutInput
      id="shipping-addressLine1"
      v-model="localAddress.addressLine1"
      label="Dirección"
      placeholder="Calle Principal 123"
      required
      :error="fieldErrors.addressLine1"
      autocomplete="address-line1"
      @blur="validateField('addressLine1')"
    />

    <CheckoutInput
      id="shipping-addressLine2"
      v-model="localAddress.addressLine2"
      label="Dirección línea 2"
      placeholder="Apartamento, suite, etc."
      autocomplete="address-line2"
    />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <CheckoutInput
        id="shipping-city"
        v-model="localAddress.city"
        label="Ciudad"
        placeholder="Madrid"
        required
        :error="fieldErrors.city"
        autocomplete="address-level2"
        @blur="validateField('city')"
      />

      <CheckoutInput
        id="shipping-state"
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
        id="shipping-postalCode"
        v-model="localAddress.postalCode"
        label="Código postal"
        placeholder="28001"
        required
        :error="fieldErrors.postalCode"
        autocomplete="postal-code"
        @blur="validateField('postalCode')"
      />

      <CheckoutInput
        id="shipping-country"
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
</template>

<script setup>
import { computed, reactive, watch } from 'vue';
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

const localAddress = reactive({
  fullName: checkoutStore.shippingAddress.fullName,
  addressLine1: checkoutStore.shippingAddress.addressLine1,
  addressLine2: checkoutStore.shippingAddress.addressLine2,
  city: checkoutStore.shippingAddress.city,
  state: checkoutStore.shippingAddress.state,
  postalCode: checkoutStore.shippingAddress.postalCode,
  country: checkoutStore.shippingAddress.country,
});

const fieldErrors = reactive({
  fullName: '',
  addressLine1: '',
  city: '',
  state: '',
  postalCode: '',
  country: '',
});

const storeErrors = computed(() => checkoutStore.errors.shipping_address || []);

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
  checkoutStore.setShippingAddress(newAddress);
}, { deep: true });
</script>

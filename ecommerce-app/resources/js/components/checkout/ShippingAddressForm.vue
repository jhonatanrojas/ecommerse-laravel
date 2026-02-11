<template>
  <div class="shipping-address-form space-y-4">
    <!-- Full Name -->
    <div>
      <label for="shipping-fullName" class="block text-sm font-medium text-gray-700 mb-1">
        Nombre Completo <span class="text-red-500">*</span>
      </label>
      <input
        id="shipping-fullName"
        v-model="localAddress.fullName"
        type="text"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        :class="{ 'border-red-500': fieldErrors.fullName }"
        placeholder="Juan Pérez"
        @blur="validateField('fullName')"
      />
      <p v-if="fieldErrors.fullName" class="mt-1 text-sm text-red-600">
        {{ fieldErrors.fullName }}
      </p>
    </div>

    <!-- Address Line 1 -->
    <div>
      <label for="shipping-addressLine1" class="block text-sm font-medium text-gray-700 mb-1">
        Dirección <span class="text-red-500">*</span>
      </label>
      <input
        id="shipping-addressLine1"
        v-model="localAddress.addressLine1"
        type="text"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        :class="{ 'border-red-500': fieldErrors.addressLine1 }"
        placeholder="Calle Principal 123"
        @blur="validateField('addressLine1')"
      />
      <p v-if="fieldErrors.addressLine1" class="mt-1 text-sm text-red-600">
        {{ fieldErrors.addressLine1 }}
      </p>
    </div>

    <!-- Address Line 2 (Optional) -->
    <div>
      <label for="shipping-addressLine2" class="block text-sm font-medium text-gray-700 mb-1">
        Dirección Línea 2 (Opcional)
      </label>
      <input
        id="shipping-addressLine2"
        v-model="localAddress.addressLine2"
        type="text"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        placeholder="Apartamento, suite, etc."
      />
    </div>

    <!-- City and State -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- City -->
      <div>
        <label for="shipping-city" class="block text-sm font-medium text-gray-700 mb-1">
          Ciudad <span class="text-red-500">*</span>
        </label>
        <input
          id="shipping-city"
          v-model="localAddress.city"
          type="text"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          :class="{ 'border-red-500': fieldErrors.city }"
          placeholder="Madrid"
          @blur="validateField('city')"
        />
        <p v-if="fieldErrors.city" class="mt-1 text-sm text-red-600">
          {{ fieldErrors.city }}
        </p>
      </div>

      <!-- State -->
      <div>
        <label for="shipping-state" class="block text-sm font-medium text-gray-700 mb-1">
          Estado/Provincia <span class="text-red-500">*</span>
        </label>
        <input
          id="shipping-state"
          v-model="localAddress.state"
          type="text"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          :class="{ 'border-red-500': fieldErrors.state }"
          placeholder="Madrid"
          @blur="validateField('state')"
        />
        <p v-if="fieldErrors.state" class="mt-1 text-sm text-red-600">
          {{ fieldErrors.state }}
        </p>
      </div>
    </div>

    <!-- Postal Code and Country -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Postal Code -->
      <div>
        <label for="shipping-postalCode" class="block text-sm font-medium text-gray-700 mb-1">
          Código Postal <span class="text-red-500">*</span>
        </label>
        <input
          id="shipping-postalCode"
          v-model="localAddress.postalCode"
          type="text"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          :class="{ 'border-red-500': fieldErrors.postalCode }"
          placeholder="28001"
          @blur="validateField('postalCode')"
        />
        <p v-if="fieldErrors.postalCode" class="mt-1 text-sm text-red-600">
          {{ fieldErrors.postalCode }}
        </p>
      </div>

      <!-- Country -->
      <div>
        <label for="shipping-country" class="block text-sm font-medium text-gray-700 mb-1">
          País <span class="text-red-500">*</span>
        </label>
        <select
          id="shipping-country"
          v-model="localAddress.country"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          :class="{ 'border-red-500': fieldErrors.country }"
          @blur="validateField('country')"
        >
          <option value="">Seleccionar país</option>
          <option value="ES">España</option>
          <option value="MX">México</option>
          <option value="AR">Argentina</option>
          <option value="CO">Colombia</option>
          <option value="CL">Chile</option>
          <option value="PE">Perú</option>
          <option value="VE">Venezuela</option>
          <option value="US">Estados Unidos</option>
        </select>
        <p v-if="fieldErrors.country" class="mt-1 text-sm text-red-600">
          {{ fieldErrors.country }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, reactive } from 'vue';
import { useCheckoutStore } from '../../stores/checkout';

const checkoutStore = useCheckoutStore();

// Local address state
const localAddress = reactive({
  fullName: checkoutStore.shippingAddress.fullName,
  addressLine1: checkoutStore.shippingAddress.addressLine1,
  addressLine2: checkoutStore.shippingAddress.addressLine2,
  city: checkoutStore.shippingAddress.city,
  state: checkoutStore.shippingAddress.state,
  postalCode: checkoutStore.shippingAddress.postalCode,
  country: checkoutStore.shippingAddress.country,
});

// Field-level errors
const fieldErrors = reactive({
  fullName: '',
  addressLine1: '',
  city: '',
  state: '',
  postalCode: '',
  country: '',
});

/**
 * Validate individual field
 */
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

/**
 * Watch for changes and update store
 */
watch(localAddress, (newAddress) => {
  checkoutStore.setShippingAddress(newAddress);
}, { deep: true });
</script>

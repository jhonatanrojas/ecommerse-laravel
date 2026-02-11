<template>
  <div class="shipping-methods space-y-3">
    <!-- Shipping Method Options -->
    <div
      v-for="method in availableMethods"
      :key="method.id"
      class="shipping-method-card border rounded-lg p-4 cursor-pointer transition-all"
      :class="{
        'border-blue-500 bg-blue-50': isSelected(method.id),
        'border-gray-300 hover:border-gray-400': !isSelected(method.id),
      }"
      @click="selectMethod(method)"
    >
      <div class="flex items-start">
        <!-- Radio Button -->
        <div class="flex items-center h-5 mt-1">
          <input
            :id="`shipping-${method.id}`"
            type="radio"
            :value="method.id"
            :checked="isSelected(method.id)"
            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
            @change="selectMethod(method)"
          />
        </div>

        <!-- Method Details -->
        <div class="ml-3 flex-1">
          <label
            :for="`shipping-${method.id}`"
            class="block text-sm font-medium text-gray-900 cursor-pointer"
          >
            {{ method.name }}
          </label>
          <p class="text-sm text-gray-600 mt-1">
            {{ method.description }}
          </p>
          <p class="text-xs text-gray-500 mt-1">
            Tiempo estimado: {{ method.estimatedDays }}
          </p>
        </div>

        <!-- Cost -->
        <div class="ml-4 text-right">
          <p class="text-lg font-semibold text-gray-900">
            {{ formatCurrency(method.cost) }}
          </p>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <p v-if="hasError" class="text-sm text-red-600 mt-2">
      {{ errorMessage }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useCheckoutStore } from '../../stores/checkout';

const checkoutStore = useCheckoutStore();

// Available shipping methods from store
const availableMethods = computed(() => checkoutStore.availableShippingMethods);

// Selected method
const selectedMethod = computed(() => checkoutStore.shippingMethod);

// Error handling
const hasError = computed(() => checkoutStore.errors.shipping_method);
const errorMessage = computed(() => {
  return checkoutStore.errors.shipping_method?.[0] || '';
});

/**
 * Check if method is selected
 */
const isSelected = (methodId) => {
  return selectedMethod.value?.id === methodId;
};

/**
 * Select shipping method
 */
const selectMethod = (method) => {
  checkoutStore.setShippingMethod(method);
};

/**
 * Format currency
 */
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount);
};
</script>

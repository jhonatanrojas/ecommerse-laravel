<template>
  <div class="payment-methods space-y-3">
    <!-- Payment Method Options -->
    <div
      v-for="method in availableMethods"
      :key="method.id"
      class="payment-method-card border rounded-lg p-4 cursor-pointer transition-all"
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
            :id="`payment-${method.id}`"
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
            :for="`payment-${method.id}`"
            class="block text-sm font-medium text-gray-900 cursor-pointer"
          >
            {{ method.name }}
          </label>
          <p class="text-sm text-gray-600 mt-1">
            {{ method.description }}
          </p>
        </div>

        <!-- Icon (optional) -->
        <div v-if="method.icon" class="ml-4">
          <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded">
            <span class="text-xs text-gray-600">{{ getIconDisplay(method.icon) }}</span>
          </div>
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

// Available payment methods from store
const availableMethods = computed(() => checkoutStore.availablePaymentMethods);

// Selected method
const selectedMethod = computed(() => checkoutStore.paymentMethod);

// Error handling
const hasError = computed(() => checkoutStore.errors.payment_method);
const errorMessage = computed(() => {
  return checkoutStore.errors.payment_method?.[0] || '';
});

/**
 * Check if method is selected
 */
const isSelected = (methodId) => {
  return selectedMethod.value?.id === methodId;
};

/**
 * Select payment method
 */
const selectMethod = (method) => {
  checkoutStore.setPaymentMethod(method);
};

/**
 * Get icon display (simplified for now)
 */
const getIconDisplay = (icon) => {
  const iconMap = {
    'credit-card': '💳',
    'paypal': 'PP',
    'bank': '🏦',
    'cash': '💵',
  };
  return iconMap[icon] || '💳';
};
</script>

<template>
  <div class="checkout-actions space-y-4">
    <!-- Submit Button -->
    <button
      type="button"
      class="w-full py-3 px-6 rounded-lg font-semibold text-white transition-all"
      :class="{
        'bg-blue-600 hover:bg-blue-700': !isDisabled,
        'bg-gray-400 cursor-not-allowed': isDisabled,
      }"
      :disabled="isDisabled"
      @click="handleSubmit"
    >
      <span v-if="!submitting" class="flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        Realizar Pedido
      </span>
      <span v-else class="flex items-center justify-center">
        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Procesando...
      </span>
    </button>

    <!-- Validation Errors -->
    <div v-if="hasValidationErrors" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-start">
        <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <div class="flex-1">
          <h4 class="text-sm font-medium text-red-800 mb-1">
            Por favor complete los siguientes campos:
          </h4>
          <ul class="text-sm text-red-700 space-y-1">
            <li v-for="(errors, field) in validationErrors" :key="field">
              <span class="font-medium">{{ getFieldLabel(field) }}:</span>
              {{ errors[0] }}
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- General Error -->
    <div v-if="generalError" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-start">
        <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm text-red-700">
          {{ generalError }}
        </p>
      </div>
    </div>

    <!-- Security Notice -->
    <div class="text-center">
      <p class="text-xs text-gray-500">
        🔒 Pago seguro y protegido
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useCheckoutStore } from '../../stores/checkout';
import { useRouter } from 'vue-router';

const checkoutStore = useCheckoutStore();
const router = useRouter();

// Emit events
const emit = defineEmits(['submit']);

// State
const submitting = computed(() => checkoutStore.submitting);
const isValid = computed(() => checkoutStore.isValid);
const isEmpty = computed(() => checkoutStore.isEmpty);
const hasErrors = computed(() => checkoutStore.hasErrors);

// Errors
const validationErrors = computed(() => {
  const errors = { ...checkoutStore.errors };
  delete errors.general;
  return errors;
});

const generalError = computed(() => {
  return checkoutStore.errors.general?.[0] || '';
});

const hasValidationErrors = computed(() => {
  return Object.keys(validationErrors.value).length > 0;
});

// Button state
const isDisabled = computed(() => {
  return submitting.value || isEmpty.value;
});

/**
 * Handle submit
 */
const handleSubmit = async () => {
  if (isDisabled.value) return;

  // Clear previous errors
  checkoutStore.clearErrors();

  // Submit checkout
  const result = await checkoutStore.submitCheckout();

  if (result.success) {
    // Emit success event
    emit('submit', result.order);
    
    // Redirect to success page
    router.push({
      name: 'order-success',
      params: { orderId: result.order.id },
    });
  }
};

/**
 * Get field label for error display
 */
const getFieldLabel = (field) => {
  const labels = {
    shipping_address: 'Dirección de envío',
    billing_address: 'Dirección de facturación',
    shipping_method: 'Método de envío',
    payment_method: 'Método de pago',
  };
  return labels[field] || field;
};
</script>

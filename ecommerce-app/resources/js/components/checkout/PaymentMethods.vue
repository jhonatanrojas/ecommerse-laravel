<template>
  <div class="space-y-3">
    <button
      v-for="method in availableMethods"
      :key="method.id"
      type="button"
      class="w-full rounded-xl border p-4 text-left transition"
      :class="isSelected(method.id)
        ? 'border-indigo-300 bg-indigo-50/70 ring-2 ring-indigo-100'
        : 'border-gray-200 bg-white hover:border-indigo-200 hover:bg-indigo-50/30'"
      @click="selectMethod(method)"
    >
      <div class="flex items-start gap-3">
        <input
          :id="`payment-${method.id}`"
          type="radio"
          :checked="isSelected(method.id)"
          class="mt-1 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
          @change="selectMethod(method)"
        />

        <div class="min-w-0 flex-1">
          <label :for="`payment-${method.id}`" class="cursor-pointer text-sm font-semibold text-gray-900">
            {{ method.name }}
          </label>
          <p class="mt-1 text-sm text-gray-600">{{ method.description }}</p>
        </div>

        <div v-if="method.icon" class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
          {{ getIconDisplay(method.icon) }}
        </div>
      </div>
    </button>

    <p v-if="hasError" class="text-sm text-red-600">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useCheckoutStore } from '../../stores/checkout';

const checkoutStore = useCheckoutStore();

const availableMethods = computed(() => checkoutStore.availablePaymentMethods);
const selectedMethod = computed(() => checkoutStore.paymentMethod);
const hasError = computed(() => checkoutStore.errors.payment_method);
const errorMessage = computed(() => checkoutStore.errors.payment_method?.[0] || '');

const isSelected = (methodId) => selectedMethod.value?.id === methodId;
const selectMethod = (method) => checkoutStore.setPaymentMethod(method);

const getIconDisplay = (icon) => {
  const iconMap = {
    'credit-card': 'Tarjeta',
    paypal: 'PayPal',
    bank: 'Banco',
    cash: 'Efectivo',
  };

  return iconMap[icon] || 'Pago';
};
</script>

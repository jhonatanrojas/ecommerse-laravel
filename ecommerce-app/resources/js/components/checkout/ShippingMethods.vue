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
          :id="`shipping-${method.id}`"
          type="radio"
          :checked="isSelected(method.id)"
          class="mt-1 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
          @change="selectMethod(method)"
        />

        <div class="min-w-0 flex-1">
          <label :for="`shipping-${method.id}`" class="cursor-pointer text-sm font-semibold text-gray-900">
            {{ method.name }}
          </label>
          <p class="mt-1 text-sm text-gray-600">{{ method.description }}</p>
          <p class="mt-1 text-xs text-gray-500">Entrega estimada: {{ method.estimatedDays }}</p>
        </div>

        <p class="text-sm font-semibold text-gray-900">{{ formatCurrency(method.cost) }}</p>
      </div>
    </button>

    <p v-if="hasError" class="text-sm text-red-600">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useCheckoutStore } from '../../stores/checkout';

const checkoutStore = useCheckoutStore();

const availableMethods = computed(() => checkoutStore.availableShippingMethods);
const selectedMethod = computed(() => checkoutStore.shippingMethod);
const hasError = computed(() => checkoutStore.errors.shipping_method);
const errorMessage = computed(() => checkoutStore.errors.shipping_method?.[0] || '');

const isSelected = (methodId) => selectedMethod.value?.id === methodId;
const selectMethod = (method) => checkoutStore.setShippingMethod(method);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount);
};
</script>

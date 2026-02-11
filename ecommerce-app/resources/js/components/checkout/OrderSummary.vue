<template>
  <div class="order-summary bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Resumen del Pedido</h2>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
      <p class="text-sm text-gray-600 mt-2">Cargando...</p>
    </div>

    <!-- Empty Cart -->
    <div v-else-if="isEmpty" class="text-center py-8">
      <p class="text-gray-600">Tu carrito está vacío</p>
    </div>

    <!-- Cart Items -->
    <div v-else class="space-y-6">
      <!-- Items List -->
      <div class="space-y-3">
        <div
          v-for="item in items"
          :key="item.uuid"
          class="flex items-start space-x-3 pb-3 border-b border-gray-200 last:border-0"
        >
          <!-- Product Image -->
          <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded overflow-hidden">
            <img
              v-if="item.product.image"
              :src="item.product.image"
              :alt="item.product.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              <span class="text-xs">Sin imagen</span>
            </div>
          </div>

          <!-- Product Details -->
          <div class="flex-1 min-w-0">
            <h3 class="text-sm font-medium text-gray-900 truncate">
              {{ item.product.name }}
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Cantidad: {{ item.quantity }}
            </p>
            <p class="text-sm text-gray-600">
              {{ formatCurrency(item.price) }} c/u
            </p>
          </div>

          <!-- Item Subtotal -->
          <div class="flex-shrink-0 text-right">
            <p class="text-sm font-medium text-gray-900">
              {{ formatCurrency(item.subtotal) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Summary Calculations -->
      <div class="space-y-2 pt-4 border-t border-gray-200">
        <!-- Subtotal -->
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Subtotal</span>
          <span class="text-gray-900">{{ formatCurrency(subtotal) }}</span>
        </div>

        <!-- Discount -->
        <div v-if="discount > 0" class="flex justify-between text-sm">
          <span class="text-gray-600">Descuento</span>
          <span class="text-green-600">-{{ formatCurrency(discount) }}</span>
        </div>

        <!-- Shipping Cost -->
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Envío</span>
          <span class="text-gray-900">
            {{ shippingCost > 0 ? formatCurrency(shippingCost) : 'Por calcular' }}
          </span>
        </div>

        <!-- Total -->
        <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200">
          <span class="text-gray-900">Total</span>
          <span class="text-gray-900">{{ formatCurrency(totalAmount) }}</span>
        </div>
      </div>

      <!-- Coupon Info -->
      <div v-if="coupon" class="bg-green-50 border border-green-200 rounded-lg p-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <span class="text-green-600 text-sm">🎉</span>
            <span class="text-sm font-medium text-green-800">
              Cupón aplicado: {{ coupon.code }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useCheckoutStore } from '../../stores/checkout';

const checkoutStore = useCheckoutStore();

// Cart data
const loading = computed(() => checkoutStore.loading);
const isEmpty = computed(() => checkoutStore.isEmpty);
const items = computed(() => checkoutStore.items);
const subtotal = computed(() => checkoutStore.subtotal);
const discount = computed(() => checkoutStore.discount);
const shippingCost = computed(() => checkoutStore.shippingCost);
const totalAmount = computed(() => checkoutStore.totalAmount);
const coupon = computed(() => checkoutStore.cart?.coupon_code ? { code: checkoutStore.cart.coupon_code } : null);

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

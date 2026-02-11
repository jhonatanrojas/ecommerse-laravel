<template>
  <div class="order-success-page min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="text-gray-600 mt-4">Cargando información del pedido...</p>
      </div>

      <!-- Success Content -->
      <div v-else-if="order" class="space-y-6">
        <!-- Success Header -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">
            ¡Pedido Realizado con Éxito!
          </h1>
          <p class="text-gray-600 mb-4">
            Gracias por tu compra. Hemos recibido tu pedido y lo procesaremos pronto.
          </p>
          <div class="inline-block bg-blue-50 border border-blue-200 rounded-lg px-6 py-3">
            <p class="text-sm text-gray-600">Número de Pedido</p>
            <p class="text-2xl font-bold text-blue-600">
              {{ order.orderNumber || order.order_number || `#${order.id}` }}
            </p>
          </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Detalles del Pedido</h2>

          <!-- Order Items -->
          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Productos</h3>
            <div class="space-y-3">
              <div
                v-for="item in orderItems"
                :key="item.id"
                class="flex items-start space-x-4 pb-3 border-b border-gray-200 last:border-0"
              >
                <!-- Product Image -->
                <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded overflow-hidden">
                  <img
                    v-if="item.product?.image"
                    :src="item.product.image"
                    :alt="item.product.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                    <span class="text-xs">Sin imagen</span>
                  </div>
                </div>

                <!-- Product Details -->
                <div class="flex-1">
                  <h4 class="text-base font-medium text-gray-900">
                    {{ item.product?.name || 'Producto' }}
                  </h4>
                  <p class="text-sm text-gray-600 mt-1">
                    Cantidad: {{ item.quantity }}
                  </p>
                  <p class="text-sm text-gray-600">
                    Precio: {{ formatCurrency(item.price) }}
                  </p>
                </div>

                <!-- Item Subtotal -->
                <div class="text-right">
                  <p class="text-base font-medium text-gray-900">
                    {{ formatCurrency(item.subtotal) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="border-t border-gray-200 pt-4">
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="text-gray-900">{{ formatCurrency(order.subtotal) }}</span>
              </div>
              <div v-if="order.discount > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Descuento</span>
                <span class="text-green-600">-{{ formatCurrency(order.discount) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Envío</span>
                <span class="text-gray-900">{{ formatCurrency(order.shippingCost || order.shipping_cost || 0) }}</span>
              </div>
              <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-200">
                <span class="text-gray-900">Total</span>
                <span class="text-gray-900">{{ formatCurrency(order.total) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping & Payment Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Shipping Address -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Dirección de Envío</h3>
            <div class="text-sm text-gray-600 space-y-1">
              <p class="font-medium text-gray-900">{{ shippingAddress.fullName || shippingAddress.full_name }}</p>
              <p>{{ shippingAddress.addressLine1 || shippingAddress.address_line_1 }}</p>
              <p v-if="shippingAddress.addressLine2 || shippingAddress.address_line_2">
                {{ shippingAddress.addressLine2 || shippingAddress.address_line_2 }}
              </p>
              <p>{{ shippingAddress.city }}, {{ shippingAddress.state }} {{ shippingAddress.postalCode || shippingAddress.postal_code }}</p>
              <p>{{ shippingAddress.country }}</p>
            </div>
          </div>

          <!-- Payment & Shipping Method -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Método de Envío y Pago</h3>
            <div class="space-y-3">
              <div>
                <p class="text-sm text-gray-600">Método de Envío</p>
                <p class="text-base font-medium text-gray-900">
                  {{ shippingMethod.name || 'Envío Estándar' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Método de Pago</p>
                <p class="text-base font-medium text-gray-900">
                  {{ paymentMethod.name || 'Tarjeta de Crédito' }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Notes -->
        <div v-if="order.notes" class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-3">Notas del Pedido</h3>
          <p class="text-sm text-gray-600">{{ order.notes }}</p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a
            href="/"
            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Volver al Inicio
          </a>
          <a
            href="/orders"
            class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Ver Mis Pedidos
          </a>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="bg-white rounded-lg shadow-md p-8 text-center">
        <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">Pedido no encontrado</h2>
        <p class="text-gray-600 mb-6">No pudimos encontrar la información de tu pedido.</p>
        <a
          href="/"
          class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors"
        >
          Volver al Inicio
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useCheckoutStore } from '../stores/checkout';

const route = useRoute();
const checkoutStore = useCheckoutStore();

// State
const loading = ref(false);
const order = computed(() => checkoutStore.order);

// Order data
const orderItems = computed(() => order.value?.items || []);
const shippingAddress = computed(() => order.value?.shippingAddress || order.value?.shipping_address || {});
const shippingMethod = computed(() => order.value?.shippingMethod || order.value?.shipping_method || {});
const paymentMethod = computed(() => order.value?.paymentMethod || order.value?.payment_method || {});

/**
 * Format currency
 */
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount);
};

/**
 * Load order on mount
 */
onMounted(() => {
  // If no order in store, could fetch from API using route.params.orderId
  if (!order.value) {
    // TODO: Fetch order from API if needed
    console.log('Order ID from route:', route.params.orderId);
  }
});
</script>

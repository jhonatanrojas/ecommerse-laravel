<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-gray-50/80">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/95 shadow-sm ring-1 ring-black/5 backdrop-blur-md">
      <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16 md:h-20">
          <a href="/" class="flex-shrink-0">
            <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-indigo-600">
              Mi Tienda
            </span>
          </a>
          <div class="flex items-center gap-2">
            <a href="/" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
              Volver al inicio
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 md:py-12">
      <!-- Loading State -->
      <div v-if="loading" class="max-w-4xl mx-auto space-y-6">
        <div class="animate-pulse space-y-6">
          <div class="h-48 bg-gray-200 rounded-3xl"></div>
          <div class="h-64 bg-gray-200 rounded-3xl"></div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="h-48 bg-gray-200 rounded-3xl"></div>
            <div class="h-48 bg-gray-200 rounded-3xl"></div>
          </div>
        </div>
      </div>

      <!-- Success Content -->
      <div v-else-if="order" class="max-w-5xl mx-auto space-y-6">
        <!-- Success Header -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl border border-green-100 p-8 md:p-12 text-center shadow-sm">
          <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-green-500/30">
            <svg class="w-11 h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3 tracking-tight">
            ¡Pedido Realizado con Éxito!
          </h1>
          <p class="text-base md:text-lg text-gray-600 mb-6 max-w-2xl mx-auto">
            Gracias por tu compra. Hemos recibido tu pedido y lo procesaremos pronto. Te enviaremos un correo con los detalles.
          </p>
          <div class="inline-flex items-center gap-3 bg-white rounded-2xl border border-green-200 px-8 py-4 shadow-sm">
            <div class="text-left">
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Número de Pedido</p>
              <p class="text-2xl md:text-3xl font-bold text-green-600 tracking-tight">
                {{ order.order_number || `#${order.id}` }}
              </p>
            </div>
          </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 md:px-8 py-5 border-b border-gray-100">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 tracking-tight">Detalles del Pedido</h2>
            <p class="text-sm text-gray-600 mt-1">Revisa los productos y el resumen de tu compra</p>
          </div>

          <div class="p-6 md:p-8">
            <!-- Order Items -->
            <div class="mb-8">
              <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Productos
              </h3>
              <div class="space-y-4">
                <div
                  v-for="item in orderItems"
                  :key="item.id"
                  class="flex items-start gap-4 p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors"
                >
                  <!-- Product Image -->
                  <div class="flex-shrink-0 w-20 h-20 md:w-24 md:h-24 bg-white rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                    <img
                      v-if="item.product?.image"
                      :src="item.product.image"
                      :alt="item.product.name"
                      class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                    </div>
                  </div>

                  <!-- Product Details -->
                  <div class="flex-1 min-w-0">
                    <h4 class="text-base md:text-lg font-semibold text-gray-900 mb-1">
                      {{ item.product?.name || 'Producto' }}
                    </h4>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-lg border border-gray-200">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Cantidad: {{ item.quantity }}
                      </span>
                      <span class="text-gray-500">
                        {{ formatCurrency(item.price) }} c/u
                      </span>
                    </div>
                  </div>

                  <!-- Item Subtotal -->
                  <div class="text-right flex-shrink-0">
                    <p class="text-lg md:text-xl font-bold text-gray-900">
                      {{ formatCurrency(item.subtotal) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-2xl p-6 border border-gray-200">
              <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Resumen de Pago
              </h3>
              <div class="space-y-3">
                <div class="flex justify-between items-center text-sm md:text-base">
                  <span class="text-gray-600">Subtotal</span>
                  <span class="font-semibold text-gray-900">{{ formatCurrency(order.subtotal || 0) }}</span>
                </div>
                <div v-if="(order.discount || 0) > 0" class="flex justify-between items-center text-sm md:text-base">
                  <span class="text-gray-600">Descuento</span>
                  <span class="font-semibold text-green-600">-{{ formatCurrency(order.discount) }}</span>
                </div>
                <div v-if="(order.tax || order.tax_amount || 0) > 0" class="flex justify-between items-center text-sm md:text-base">
                  <span class="text-gray-600">Impuestos</span>
                  <span class="font-semibold text-gray-900">{{ formatCurrency(order.tax || order.tax_amount || 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm md:text-base">
                  <span class="text-gray-600">Envío</span>
                  <span class="font-semibold text-gray-900">
                    {{ formatCurrency(order.shipping_cost || order.shipping_amount || 0) }}
                  </span>
                </div>
                <div class="pt-3 border-t-2 border-gray-300">
                  <div class="flex justify-between items-center">
                    <span class="text-lg md:text-xl font-bold text-gray-900">Total</span>
                    <span class="text-2xl md:text-3xl font-extrabold text-indigo-600">
                      {{ formatCurrency(order.total || 0) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping & Payment Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Shipping Address -->
          <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b border-gray-100">
              <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Dirección de Envío
              </h3>
            </div>
            <div class="p-6">
              <div class="space-y-2 text-sm md:text-base text-gray-600">
                <p class="font-semibold text-gray-900 text-base">
                  {{ shippingAddress.fullName || shippingAddress.full_name }}
                </p>
                <p>{{ shippingAddress.addressLine1 || shippingAddress.address_line_1 }}</p>
                <p v-if="shippingAddress.addressLine2 || shippingAddress.address_line_2">
                  {{ shippingAddress.addressLine2 || shippingAddress.address_line_2 }}
                </p>
                <p>
                  {{ shippingAddress.city }}, {{ shippingAddress.state }} 
                  {{ shippingAddress.postalCode || shippingAddress.postal_code }}
                </p>
                <p class="font-medium text-gray-700">{{ shippingAddress.country }}</p>
              </div>
            </div>
          </div>

          <!-- Payment & Shipping Method -->
          <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-100">
              <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Métodos de Pago y Envío
              </h3>
            </div>
            <div class="p-6 space-y-5">
              <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Método de Envío</p>
                <p class="text-base font-semibold text-gray-900 flex items-center gap-2">
                  <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                  </svg>
                  {{ shippingMethod.name || 'Envío Estándar' }}
                </p>
              </div>
              <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Método de Pago</p>
                <p class="text-base font-semibold text-gray-900 flex items-center gap-2">
                  <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  {{ paymentMethod.name || 'Tarjeta de Crédito' }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Notes -->
        <div v-if="order.notes || order.customer_notes" class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
              </svg>
              Notas del Pedido
            </h3>
          </div>
          <div class="p-6">
            <p class="text-sm md:text-base text-gray-600 leading-relaxed">{{ order.notes || order.customer_notes }}</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
          <a
            href="/"
            class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 text-white font-semibold rounded-2xl hover:bg-indigo-700 transition-all duration-200 shadow-lg shadow-indigo-600/30 hover:shadow-xl hover:shadow-indigo-600/40 hover:-translate-y-0.5"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Volver al Inicio
          </a>
          <a
            href="/customer"
            class="inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-2xl hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Ver Mis Pedidos
          </a>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 md:p-12 text-center">
          <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 tracking-tight">Pedido no encontrado</h2>
          <p class="text-base text-gray-600 mb-8 max-w-md mx-auto">
            No pudimos encontrar la información de tu pedido. Por favor, verifica el número de pedido o contacta con soporte.
          </p>
          <a
            href="/"
            class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 text-white font-semibold rounded-2xl hover:bg-indigo-700 transition-all duration-200 shadow-lg shadow-indigo-600/30"
          >
            Volver al Inicio
          </a>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
      <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="text-center space-y-4">
          <p class="text-sm text-gray-400">
            ¿Necesitas ayuda? <a href="/contact" class="text-indigo-400 hover:text-indigo-300 font-medium">Contáctanos</a>
          </p>
          <p class="text-xs text-gray-500">
            &copy; {{ currentYear }} Mi Tienda. Todos los derechos reservados.
          </p>
        </div>
      </div>
    </footer>
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
const currentYear = new Date().getFullYear();

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
onMounted(async () => {
  // If no order in store, try to load from API using route.params.orderId
  if (!order.value && route.params.orderId) {
    loading.value = true;
    try {
      // Try to get order from API
      const token = localStorage.getItem('auth_token');
      const response = await fetch(`/api/customer/orders/${route.params.orderId}`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        },
      });
      
      if (response.ok) {
        const data = await response.json();
        // Store the order in checkout store
        checkoutStore.order = data.data || data;
      } else {
        console.error('Failed to load order:', response.status);
      }
    } catch (error) {
      console.error('Error loading order:', error);
    } finally {
      loading.value = false;
    }
  }
});
</script>

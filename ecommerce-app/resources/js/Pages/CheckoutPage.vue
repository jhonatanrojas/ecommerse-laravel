<template>
  <div class="checkout-page min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Finalizar Compra</h1>
        <p class="text-gray-600 mt-2">Complete la información para procesar su pedido</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="text-gray-600 mt-4">Cargando carrito...</p>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="isEmpty" class="bg-white rounded-lg shadow-md p-8 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">Tu carrito está vacío</h2>
        <p class="text-gray-600 mb-6">Agrega productos antes de proceder al checkout</p>
        <a
          href="/"
          class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors"
        >
          Ir a la tienda
        </a>
      </div>

      <!-- Checkout Content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content (Left Column) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Customer Data Section -->
          <section class="checkout-section">
            <CustomerDataSection />
          </section>

          <!-- Shipping Address -->
          <section class="checkout-section bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
              Dirección de Envío
            </h2>
            <ShippingAddressForm />
          </section>

          <!-- Billing Address -->
          <section class="checkout-section bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
              Dirección de Facturación
            </h2>
            <BillingAddressForm />
          </section>

          <!-- Shipping Methods -->
          <section class="checkout-section bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
              Método de Envío
            </h2>
            <ShippingMethods />
          </section>

          <!-- Payment Methods -->
          <section class="checkout-section bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
              Método de Pago
            </h2>
            <PaymentMethods />
          </section>

          <!-- Order Notes -->
          <section class="checkout-section bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
              Notas del Pedido (Opcional)
            </h2>
            <textarea
              v-model="notes"
              rows="4"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
              placeholder="¿Alguna instrucción especial para tu pedido?"
            ></textarea>
          </section>
        </div>

        <!-- Sidebar (Right Column) -->
        <div class="lg:col-span-1">
          <div class="sticky top-8 space-y-6">
            <!-- Order Summary -->
            <OrderSummary />

            <!-- Checkout Actions -->
            <CheckoutActions @submit="handleOrderSuccess" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCheckoutStore } from '../stores/checkout';
import { useAuthStore } from '../stores/auth';
import CustomerDataSection from '../components/checkout/CustomerDataSection.vue';
import ShippingAddressForm from '../components/checkout/ShippingAddressForm.vue';
import BillingAddressForm from '../components/checkout/BillingAddressForm.vue';
import ShippingMethods from '../components/checkout/ShippingMethods.vue';
import PaymentMethods from '../components/checkout/PaymentMethods.vue';
import OrderSummary from '../components/checkout/OrderSummary.vue';
import CheckoutActions from '../components/checkout/CheckoutActions.vue';

const router = useRouter();
const checkoutStore = useCheckoutStore();

// State
const loading = computed(() => checkoutStore.loading);
const isEmpty = computed(() => checkoutStore.isEmpty);
const notes = computed({
  get: () => checkoutStore.notes,
  set: (value) => checkoutStore.setNotes(value),
});

/**
 * Load cart and store config on mount
 */
onMounted(async () => {
  await checkoutStore.loadStoreConfig();
  await checkoutStore.loadCart();
  
  // If guest checkout is not allowed and user is not authenticated, redirect to login
  if (!checkoutStore.allowGuestCheckout && !checkoutStore.isEmpty) {
    const authStore = useAuthStore();
    if (!authStore.isAuthenticated) {
      window.location.href = '/login?redirect=/checkout';
    }
  }
});

/**
 * Handle order success
 */
const handleOrderSuccess = (order) => {
  console.log('Order submitted successfully:', order);
};
</script>

<style scoped>
.checkout-section {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

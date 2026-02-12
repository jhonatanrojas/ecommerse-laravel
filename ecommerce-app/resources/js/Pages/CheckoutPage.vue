<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-gray-50/80 py-8 sm:py-10">
    <div class="container mx-auto px-4">
      <header class="mb-8 sm:mb-10">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Checkout</p>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">Finalizar compra</h1>
        <p class="mt-2 max-w-2xl text-sm text-gray-500 md:text-base">
          Completa tus datos de envío y pago para confirmar el pedido.
        </p>
      </header>

      <div v-if="loading" class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-7">
          <div v-for="n in 4" :key="`checkout-loading-form-${n}`" class="animate-pulse rounded-2xl border border-gray-100 bg-white p-6 shadow-md">
            <div class="h-4 w-40 rounded bg-gray-100"></div>
            <div class="mt-3 h-3 w-64 rounded bg-gray-100"></div>
            <div class="mt-6 h-11 rounded-xl bg-gray-100"></div>
            <div class="mt-3 h-11 rounded-xl bg-gray-100"></div>
          </div>
        </div>
        <div class="lg:col-span-5">
          <div class="animate-pulse rounded-2xl border border-gray-100 bg-white p-6 shadow-md">
            <div class="h-4 w-40 rounded bg-gray-100"></div>
            <div class="mt-6 space-y-3">
              <div class="h-16 rounded-xl bg-gray-100"></div>
              <div class="h-16 rounded-xl bg-gray-100"></div>
              <div class="h-12 rounded-xl bg-gray-100"></div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="isEmpty" class="mx-auto max-w-xl rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-md">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
          <svg class="h-7 w-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Tu carrito está vacío</h2>
        <p class="mt-2 text-sm text-gray-500">Agrega productos al carrito antes de iniciar el checkout.</p>
        <a href="/" class="btn-primary mt-6 inline-flex px-6 py-3 text-sm">Volver a la tienda</a>
      </div>

      <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-12 xl:gap-8">
        <div class="lg:col-span-7">
          <CheckoutForm />
        </div>

        <aside class="lg:col-span-5">
          <div class="space-y-4 lg:sticky lg:top-24">
            <CheckoutSummary @submit="handleOrderSuccess" />
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useCheckoutStore } from '../stores/checkout';
import { useAuthStore } from '../stores/auth';
import CheckoutForm from '../components/checkout/CheckoutForm.vue';
import CheckoutSummary from '../components/checkout/CheckoutSummary.vue';

const checkoutStore = useCheckoutStore();

const loading = computed(() => checkoutStore.loading);
const isEmpty = computed(() => checkoutStore.isEmpty);

onMounted(async () => {
  const authStore = useAuthStore();
  await authStore.checkAuth();

  await checkoutStore.loadStoreConfig();
  await checkoutStore.loadCart();

  if (!checkoutStore.allowGuestCheckout && !checkoutStore.isEmpty && !authStore.isAuthenticated) {
    window.location.href = '/login?redirect=/checkout';
  }
});

const handleOrderSuccess = (order) => {
  console.log('Order submitted successfully:', order);
};
</script>

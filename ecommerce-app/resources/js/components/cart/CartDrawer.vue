<template>
  <Teleport to="body">
    <!-- Overlay -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="cartStore.isDrawerOpen"
        class="fixed inset-0 bg-black/50 z-50"
        @click="cartStore.closeDrawer()"
      ></div>
    </Transition>

    <!-- Drawer -->
    <Transition
      enter-active-class="transition-transform duration-300"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-300"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div
        v-if="cartStore.isDrawerOpen"
        class="fixed right-0 top-0 h-full w-full sm:w-[440px] bg-white shadow-2xl z-50 flex flex-col"
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
          <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h2 class="text-lg font-bold text-gray-900">
              Mi Carrito
              <span v-if="cartStore.itemCount > 0" class="text-sm font-normal text-gray-500">
                ({{ cartStore.itemCount }} {{ cartStore.itemCount === 1 ? 'artículo' : 'artículos' }})
              </span>
            </h2>
          </div>
          <button
            @click="cartStore.closeDrawer()"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            aria-label="Cerrar carrito"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="cartStore.loading" class="flex-1 flex items-center justify-center">
          <div class="text-center">
            <div class="w-12 h-12 border-3 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3"></div>
            <p class="text-sm text-gray-500">Cargando carrito...</p>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="cartStore.isEmpty" class="flex-1 flex items-center justify-center px-6">
          <div class="text-center max-w-xs">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tu carrito está vacío</h3>
            <p class="text-sm text-gray-500 mb-6">Añade productos para comenzar tu compra</p>
            <button
              @click="cartStore.closeDrawer()"
              class="btn-primary w-full"
            >
              Continuar comprando
            </button>
          </div>
        </div>

        <!-- Cart Items -->
        <div v-else class="flex-1 flex flex-col overflow-hidden">
          <!-- Items List -->
          <div class="flex-1 overflow-y-auto px-6 py-4">
            <TransitionGroup
              name="list"
              tag="div"
              class="space-y-4"
            >
              <CartItem
                v-for="item in cartStore.items"
                :key="item.uuid"
                :item="item"
              />
            </TransitionGroup>
          </div>

          <!-- Coupon Section -->
          <div class="px-6 py-4 border-t border-gray-200">
            <CouponInput />
          </div>

          <!-- Summary -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="space-y-2 mb-4">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium text-gray-900">${{ formatPrice(cartStore.subtotal) }}</span>
              </div>
              <div v-if="cartStore.discount > 0" class="flex justify-between text-sm">
                <span class="text-green-600">Descuento</span>
                <span class="font-medium text-green-600">-${{ formatPrice(cartStore.discount) }}</span>
              </div>
              <div v-if="cartStore.tax > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Impuestos</span>
                <span class="font-medium text-gray-900">${{ formatPrice(cartStore.tax) }}</span>
              </div>
              <div v-if="cartStore.shippingCost > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Envío</span>
                <span class="font-medium text-gray-900">${{ formatPrice(cartStore.shippingCost) }}</span>
              </div>
              <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                <span class="text-gray-900">Total</span>
                <span class="text-indigo-600">${{ formatPrice(cartStore.total) }}</span>
              </div>
            </div>

            <button
              @click="goToCheckout"
              class="btn-primary w-full mb-3"
            >
              Ir al Checkout
            </button>

            <button
              @click="handleClearCart"
              :disabled="cartStore.loading"
              class="w-full px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50"
            >
              Vaciar carrito
            </button>
          </div>
        </div>

        <!-- Toast Notification -->
        <Transition
          enter-active-class="transition-all duration-300"
          enter-from-class="opacity-0 translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition-all duration-200"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 translate-y-2"
        >
          <div
            v-if="showToast"
            class="absolute top-20 left-4 right-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 shadow-lg"
          >
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="text-sm text-red-800 flex-1">{{ cartStore.error }}</p>
              <button @click="showToast = false" class="text-red-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useCartStore } from '../../stores/cart';
import CartItem from './CartItem.vue';
import CouponInput from './CouponInput.vue';

const cartStore = useCartStore();
const showToast = ref(false);

// Watch for errors
watch(() => cartStore.error, (newError) => {
  if (newError) {
    showToast.value = true;
    setTimeout(() => {
      showToast.value = false;
    }, 5000);
  }
});

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2);
};

const goToCheckout = () => {
  window.location.href = '/checkout';
};

const handleClearCart = async () => {
  if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
    await cartStore.clearCart();
  }
};
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
.list-move {
  transition: transform 0.3s ease;
}
</style>

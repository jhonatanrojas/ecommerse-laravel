<template>
  <button
    @click="cartStore.toggleDrawer()"
    class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
    aria-label="Carrito de compras"
  >
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
    </svg>
    
    <!-- Badge with animation -->
    <Transition
      enter-active-class="transition-all duration-200"
      enter-from-class="opacity-0 scale-0"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition-all duration-150"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-0"
    >
      <span
        v-if="cartStore.itemCount > 0"
        class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-indigo-600 text-white text-xs rounded-full flex items-center justify-center font-bold"
        :class="{ 'animate-bounce': justAdded }"
      >
        {{ cartStore.itemCount > 99 ? '99+' : cartStore.itemCount }}
      </span>
    </Transition>
  </button>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useCartStore } from '../../stores/cart';

const cartStore = useCartStore();
const justAdded = ref(false);

// Animate badge when item count increases
watch(() => cartStore.itemCount, (newCount, oldCount) => {
  if (newCount > oldCount) {
    justAdded.value = true;
    setTimeout(() => {
      justAdded.value = false;
    }, 1000);
  }
});
</script>

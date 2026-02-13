<template>
  <header
    class="fixed inset-x-0 top-0 z-[90] border-b border-gray-200 bg-white/95 backdrop-blur transition-transform duration-300 md:hidden"
    :class="visible ? 'translate-y-0 shadow-sm' : '-translate-y-full'"
  >
    <div class="flex items-center justify-between px-4 py-3">
      <button type="button" class="rounded-lg p-1.5 text-gray-700 hover:bg-gray-100" aria-label="Abrir menu" @click="toggleMenu">☰</button>
      <a href="/" class="text-base font-black text-gray-900">Mi Tienda</a>
      <div class="flex items-center gap-3 text-sm">
        <a href="/wishlist" class="relative rounded-lg p-1 text-gray-700 hover:bg-gray-100" aria-label="Wishlist">♡<span v-if="wishlistCount" class="absolute -right-1 -top-1 rounded-full bg-red-500 px-1 text-[10px] text-white">{{ wishlistCount }}</span></a>
        <a href="/cart" class="relative rounded-lg p-1 text-gray-700 hover:bg-gray-100" aria-label="Carrito">🛒<span v-if="cartCount" class="absolute -right-1 -top-1 rounded-full bg-[var(--mp-accent)] px-1 text-[10px] text-white">{{ cartCount }}</span></a>
      </div>
    </div>

    <div class="px-4 pb-3">
      <SmartSearch sticky-on-mobile @search="$emit('search', $event)" @navigate="$emit('navigate', $event)" />
    </div>

    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="menuOpen" class="border-t border-gray-100 bg-white px-4 pb-4 pt-2">
        <Navigation location="mobile" variant="mobile" />
      </div>
    </transition>
  </header>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import Navigation from '../../Components/Navigation.vue';
import SmartSearch from '../shared/SmartSearch.vue';

defineProps({
  cartCount: { type: Number, default: 0 },
  wishlistCount: { type: Number, default: 0 },
});

defineEmits(['toggle-sidebar', 'search', 'navigate']);

const visible = ref(true);
const menuOpen = ref(false);
let lastY = 0;

function onScroll() {
  const y = window.scrollY || 0;

  if (y < 100) {
    visible.value = true;
  } else if (y > lastY) {
    visible.value = false;
  } else {
    visible.value = true;
  }

  lastY = y;
}

onMounted(() => {
  lastY = window.scrollY || 0;
  window.addEventListener('scroll', onScroll, { passive: true });
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll);
});

function toggleMenu() {
  menuOpen.value = !menuOpen.value;
}
</script>

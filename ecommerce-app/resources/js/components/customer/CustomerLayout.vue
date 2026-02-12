<template>
  <div class="min-h-screen bg-gradient-to-b from-white via-gray-50 to-gray-100/60">
    <header
      class="sticky top-0 z-50 border-b border-gray-200/80 bg-white/90 backdrop-blur-md"
      :class="scrolled ? 'shadow-sm ring-1 ring-black/5' : ''"
    >
      <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between gap-4">
          <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 transition hover:text-indigo-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a la tienda
          </a>

          <div class="flex items-center gap-2 sm:gap-4">
            <div class="hidden text-right sm:block">
              <p class="text-sm font-semibold text-gray-900">{{ customerStore.user?.name || 'Cliente' }}</p>
              <p class="text-xs text-gray-500">{{ customerStore.user?.email || '' }}</p>
            </div>

            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50"
              :disabled="loggingOut"
              @click="handleLogout"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              <span class="hidden sm:inline">{{ loggingOut ? 'Saliendo...' : 'Cerrar sesión' }}</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8 sm:py-10">
      <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
          <p v-if="eyebrow" class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">{{ eyebrow }}</p>
          <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">
            {{ title }}
          </h1>
          <p v-if="subtitle" class="mt-2 text-sm text-gray-500 sm:text-base">{{ subtitle }}</p>
        </div>
        <slot name="header-actions"></slot>
      </div>

      <div v-if="logoutError" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        {{ logoutError }}
      </div>

      <slot></slot>
    </main>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useCustomerStore } from '../../stores/customer';

defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  eyebrow: {
    type: String,
    default: '',
  },
});

const authStore = useAuthStore();
const customerStore = useCustomerStore();

const scrolled = ref(false);
const loggingOut = ref(false);
const logoutError = ref('');

const handleScroll = () => {
  scrolled.value = window.scrollY > 6;
};

const handleLogout = async () => {
  logoutError.value = '';
  loggingOut.value = true;

  const result = await authStore.logout();
  if (result.success) {
    window.location.href = '/';
    return;
  }

  logoutError.value = result.message || 'No se pudo cerrar sesión. Intenta de nuevo.';
  loggingOut.value = false;
};

onMounted(() => {
  handleScroll();
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

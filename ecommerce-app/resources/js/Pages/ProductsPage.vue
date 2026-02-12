<template>
  <div class="min-h-screen bg-white text-gray-900">
    <header class="sticky top-0 z-50 bg-white/95 shadow-sm backdrop-blur-md">
      <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between gap-4">
          <a href="/" class="text-xl font-extrabold tracking-tight" style="color: var(--color-primary);">
            {{ appName }}
          </a>

          <div class="hidden lg:block">
            <Navigation location="header" variant="header" />
          </div>

          <div class="flex items-center gap-2">
            <a :href="accountUrl" class="rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900" aria-label="Mi cuenta">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </a>
            <CartButton />
          </div>
        </div>
      </div>
    </header>

    <main>
      <HomeProductsSection @show-toast="showToast" />
    </main>

    <footer class="bg-gray-900 text-white">
      <div class="container mx-auto px-4 py-10 md:py-12">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
          <div>
            <p class="text-lg font-bold">{{ appName }}</p>
            <p class="mt-2 text-sm text-gray-400">Explora nuestro catalogo completo y compra sin friccion.</p>
          </div>
          <div>
            <p class="mb-3 text-sm font-bold uppercase tracking-wider text-white">Navegacion</p>
            <Navigation location="footer" variant="footer" />
          </div>
          <div>
            <p class="mb-3 text-sm font-bold uppercase tracking-wider text-white">Soporte</p>
            <p class="text-sm text-gray-400">info@tutienda.com</p>
            <p class="text-sm text-gray-400">(123) 456-7890</p>
          </div>
        </div>
      </div>
    </footer>

    <CartDrawer />

    <CartToast
      :show="toast.show"
      :type="toast.type"
      :title="toast.title"
      :message="toast.message"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, reactive } from 'vue';
import Navigation from '../Components/Navigation.vue';
import HomeProductsSection from '../components/home/HomeProductsSection.vue';
import CartButton from '../components/cart/CartButton.vue';
import CartDrawer from '../components/cart/CartDrawer.vue';
import CartToast from '../components/cart/CartToast.vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';

const appName = 'Mi Tienda';
const cartStore = useCartStore();
const authStore = useAuthStore();

const toast = reactive({
  show: false,
  type: 'info',
  title: '',
  message: '',
});

const accountUrl = computed(() => (authStore.isAuthenticated ? '/customer' : '/login'));

onMounted(async () => {
  await Promise.all([
    cartStore.fetchCart(),
    authStore.checkAuth(),
  ]);
});

function showToast(type, message, title = '') {
  toast.show = true;
  toast.type = type;
  toast.message = message;
  toast.title = title;
}
</script>

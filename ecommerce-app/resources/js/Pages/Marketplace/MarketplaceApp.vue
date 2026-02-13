<template>
  <AppLayout class="mp-shell min-h-screen text-gray-900">
    <template #header>
      <MobileHeader @search="onSearch" @navigate="onNavigate" />
      <header class="mp-topbar sticky top-0 z-50 hidden md:block">
        <div class="container mx-auto px-4 py-3">
          <div class="flex items-center gap-4">
            <a href="/" class="min-w-fit text-xl font-black tracking-tight text-gray-900">Mi Tienda</a>

            <SmartSearch
              v-model="searchQuery"
              class="mx-auto max-w-3xl flex-1"
              @search="onSearch"
              @navigate="onNavigate"
            />

            <div class="flex items-center gap-2 text-sm">
              <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" aria-label="Notificaciones">🔔</button>
              <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" aria-label="Favoritos">♡</button>
              <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" aria-label="Mensajes">💬</button>
              <a href="/customer" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" aria-label="Perfil">👤</a>
            </div>
          </div>

          <div class="mt-3 flex items-center gap-5 text-sm font-medium text-gray-600">
            <a href="/categories" class="hover:text-gray-900">Categorias</a>
            <a href="/customer/orders" class="hover:text-gray-900">Historial</a>
            <a href="/deals" class="hover:text-gray-900">Ofertas</a>
            <a href="/marketplace/vendors/register" class="hover:text-gray-900">Vender</a>
            <a href="/contact" class="hover:text-gray-900">Ayuda</a>
            <span class="mp-highlight">Marketplace</span>
          </div>
        </div>
      </header>
    </template>

    <div class="pt-0 md:pt-0">
      <router-view />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '../../components/layout/AppLayout.vue';
import MobileHeader from '../../components/layout/MobileHeader.vue';
import SmartSearch from '../../components/shared/SmartSearch.vue';

const searchQuery = ref('');

function onSearch(query) {
  if (!query) return;
  window.location.href = `/marketplace/search?q=${encodeURIComponent(query)}`;
}

function onNavigate(path) {
  if (path) window.location.href = path;
}
</script>

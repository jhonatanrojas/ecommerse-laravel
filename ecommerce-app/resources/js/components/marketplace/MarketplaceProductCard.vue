<template>
  <article class="mp-card group overflow-hidden" role="article">
    <router-link :to="`/marketplace/products/${product.slug}`" class="block">
      <div class="relative aspect-square overflow-hidden bg-gray-100">
        <img
          v-if="mainImage"
          :src="mainImage"
          :alt="product.name"
          class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
        >
      </div>

      <div class="space-y-2 p-4">
        <div class="space-y-0.5">
          <p v-if="product.compare_price" class="text-sm text-gray-400 line-through">{{ formatPrice(product.compare_price) }}</p>
          <p class="mp-price text-3xl">{{ formatPrice(product.price) }}</p>
          <p class="text-xs font-medium text-gray-500">{{ formatBs(product.price) }}</p>
          <p v-if="discount > 0" class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-700">{{ discount }}% OFF</p>
        </div>

        <p class="line-clamp-2 min-h-[42px] text-sm font-semibold text-gray-800">{{ product.name }}</p>

        <div class="space-y-1 text-xs text-gray-600">
          <p class="font-semibold text-green-700">🚚 {{ product.free_shipping ? 'Envio gratis' : 'Envio nacional' }}</p>
          <p>⏱ {{ product.estimated_delivery || 'Llega entre 2 y 5 dias' }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-2 text-xs text-gray-600">
          <p class="font-semibold text-gray-700">{{ product.seller?.name || product.vendor?.business_name || 'Vendedor' }}</p>
          <p>📍 {{ product.seller?.location || product.vendor?.location || 'Ubicacion no disponible' }}</p>
          <p class="text-amber-500">{{ stars }} <span class="ml-1 text-gray-600">{{ rating.toFixed(1) }}</span></p>
        </div>

        <div class="space-y-1 text-xs text-gray-600">
          <p>⚠ Solo {{ product.stock || 0 }} disponibles</p>
          <p>👁 {{ product.views_count || 24 }} personas lo estan viendo</p>
        </div>

        <div class="grid grid-cols-2 gap-1 text-xs text-gray-600">
          <p>🛡 Garantia</p>
          <p>🔒 Pago seguro</p>
          <p>💬 Soporte</p>
          <p>⚡ Entrega rapida</p>
        </div>
      </div>
    </router-link>
  </article>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const mainImage = computed(() => props.product.image || props.product.images?.[0]?.url || null);
const rating = computed(() => Number(props.product.rating || 0));
const discount = computed(() => Number(props.product.discount_percentage || 0));
const stars = computed(() => {
  const full = Math.round(rating.value);
  return `${'★'.repeat(full)}${'☆'.repeat(Math.max(0, 5 - full))}`;
});

function formatPrice(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
}

function formatBs(value) {
  const amount = Number(value || 0) * 36.5;
  return new Intl.NumberFormat('es-VE', { style: 'currency', currency: 'VES', maximumFractionDigits: 2 }).format(amount);
}
</script>

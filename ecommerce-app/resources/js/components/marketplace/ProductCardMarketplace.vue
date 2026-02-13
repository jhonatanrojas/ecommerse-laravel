<template>
  <article class="mp-card overflow-hidden p-4" role="article">
    <a :href="`/marketplace/products/${product.slug}`" class="flex flex-col gap-4 md:flex-row">
      <section class="w-full md:w-[250px]">
        <img class="lazyload aspect-square w-full rounded-xl object-cover" :data-src="image" :alt="product.name">
      </section>

      <section class="flex-1">
        <div class="space-y-1">
          <p v-if="product.compare_price" class="text-sm text-gray-400 line-through">{{ formatPrice(product.compare_price) }}</p>
          <p class="mp-price text-4xl">{{ formatPrice(product.price) }}</p>
          <p class="text-sm font-medium text-gray-500">{{ formatBs(product.price) }}</p>
          <span v-if="discount > 0" class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-bold text-emerald-700">-{{ discount }}% OFF</span>
        </div>

        <h3 class="mt-3 line-clamp-2 text-xl font-semibold text-gray-900 hover:text-[var(--mp-accent)]">{{ product.name }}</h3>

        <p class="mt-2 text-sm text-gray-600">
          <span class="text-amber-500">{{ stars }}</span>
          <span class="ml-1">{{ rating.toFixed(1) }}</span>
          <span class="mx-1">•</span>
          {{ reviews }} opiniones
        </p>

        <div class="mt-3 space-y-1 text-sm text-gray-600">
          <p class="font-semibold text-green-700">🚚 {{ product.free_shipping ? 'Envio gratis' : 'Envio nacional' }}</p>
          <p>⏱ {{ product.estimated_delivery || 'Llega entre 2 y 5 dias' }}</p>
        </div>

        <div class="mt-3 flex items-center gap-2">
          <img :src="sellerLogo" :alt="sellerName" class="lazyload h-9 w-9 rounded-full border border-gray-200 object-cover" :data-src="sellerLogo">
          <div class="text-sm text-gray-700">
            <p class="font-semibold">{{ sellerName }}</p>
            <p class="text-xs text-gray-500">📍 {{ product.seller?.location || 'Sin ubicacion' }}</p>
          </div>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
          <span>⭐ {{ Number(product.seller?.rating || product.seller_rating || 0).toFixed(1) }}/5</span>
          <span>•</span>
          <span>{{ product.seller?.sales_count || product.seller_sales_count || 0 }} ventas</span>
          <span>•</span>
          <span>{{ product.seller?.on_time_delivery_rate || product.seller_on_time_delivery_rate || 0 }}% a tiempo</span>
        </div>

        <div class="mt-3 space-y-1 text-xs text-gray-600">
          <p>⚠ Solo {{ product.stock || 0 }} disponibles</p>
          <p>👁 {{ product.views_count || 24 }} personas lo estan viendo</p>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-1 text-xs text-gray-600 md:max-w-sm">
          <p>🛡 Garantia</p>
          <p>🔒 Pago seguro</p>
          <p>💬 Soporte</p>
          <p>⚡ Entrega rapida</p>
        </div>
      </section>
    </a>
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

const image = computed(() => props.product.image || props.product.images?.[0]?.url || '');
const sellerLogo = computed(() => props.product.seller?.logo || image.value);
const sellerName = computed(() => props.product.seller?.name || props.product.vendor?.business_name || 'Vendedor');
const rating = computed(() => Number(props.product.rating || 4.7));
const reviews = computed(() => Number(props.product.reviews_count || 156));
const discount = computed(() => Number(props.product.discount_percentage || 0));
const stars = computed(() => {
  const rounded = Math.round(rating.value);
  return `${'★'.repeat(rounded)}${'☆'.repeat(Math.max(0, 5 - rounded))}`;
});

function formatPrice(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
}

function formatBs(value) {
  const amount = Number(value || 0) * 36.5;
  return new Intl.NumberFormat('es-VE', { style: 'currency', currency: 'VES', maximumFractionDigits: 2 }).format(amount);
}
</script>

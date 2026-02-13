<template>
  <article
    class="product-card group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-sm transition duration-300 hover:shadow-xl focus-within:ring-2 focus-within:ring-blue-500"
    role="article"
    tabindex="0"
    :aria-labelledby="titleId"
    :aria-label="`${product.name} - ${formatPrice(product.price)}`"
    @keydown.enter.prevent="openProduct"
    @keydown.space.prevent="handleAddToCart"
  >
    <div class="absolute left-3 top-3 z-20 flex flex-col gap-1.5">
      <span v-if="product.is_hot" class="badge badge-hot">HOT</span>
      <span v-if="product.is_new" class="badge badge-new">NUEVO</span>
      <span v-if="discountPercentage > 0" class="badge badge-discount">-{{ discountPercentage }}%</span>
    </div>

    <button
      type="button"
      class="absolute right-3 top-3 z-20 rounded-full bg-white/95 p-2 shadow transition hover:scale-110"
      :aria-label="inWishlist ? 'Quitar de favoritos' : 'Agregar a favoritos'"
      @click="toggleWishlist"
    >
      <span v-if="inWishlist" class="text-red-500">❤</span>
      <span v-else class="text-gray-700">♡</span>
    </button>

    <div class="relative overflow-hidden rounded-xl bg-gray-100">
      <img
        class="lazyload aspect-square w-full object-cover transition duration-300 group-hover:scale-110"
        :data-src="product.image"
        :alt="product.name"
      >

      <button
        type="button"
        class="absolute inset-0 hidden items-center justify-center bg-black/50 text-sm font-bold text-white opacity-0 transition group-hover:opacity-100 md:flex"
        @click="$emit('quick-view', product)"
      >
        Vista Rapida
      </button>
    </div>

    <div class="mt-3 space-y-2">
      <a
        :href="`/products/${product.slug}`"
        class="flex items-center gap-2 text-xs text-gray-500 hover:text-gray-700"
        @click.prevent="openSeller"
      >
        <img :src="product.seller?.logo" :alt="product.seller?.name || 'Vendedor'" class="h-6 w-6 rounded-full border border-gray-200 object-cover lazyload" :data-src="product.seller?.logo">
        <span class="truncate">{{ product.seller?.name || 'Vendedor' }}</span>
        <svg v-if="product.seller?.verified" class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m9.55 16.26-3.8-3.8-1.41 1.42 5.21 5.2L20.66 7.97l-1.41-1.41z"/></svg>
      </a>

      <a :id="titleId" :href="`/products/${product.slug}`" class="line-clamp-2 text-sm font-semibold text-gray-800 hover:text-blue-600" @click.prevent="openProduct">
        {{ product.name }}
      </a>

      <p class="text-xs text-gray-500" :aria-label="`Calificacion ${ratingValue} de 5 estrellas`">
        <span aria-hidden="true" class="text-amber-400">{{ starString }}</span>
        <span class="ml-1">({{ ratingValue.toFixed(1) }})</span>
        <span class="mx-1">•</span>
        <span>{{ product.sales_count || 0 }} ventas</span>
      </p>

      <div>
        <p v-if="product.compare_price" class="text-sm text-gray-400 line-through">{{ formatPrice(product.compare_price) }}</p>
        <p class="text-2xl font-black text-blue-700">{{ formatPrice(product.price) }}</p>
        <p v-if="product.installments?.count" class="text-xs font-semibold text-green-600">
          {{ product.installments.count }} x {{ formatPrice(product.installments.amount) }}
        </p>
      </div>

      <p v-if="product.free_shipping" class="text-xs">
        <span class="font-bold text-green-700">🚚 Envio gratis</span>
        <span class="ml-1 text-gray-500">{{ product.estimated_delivery || '' }}</span>
      </p>

      <p v-if="showLowStock" class="text-xs font-semibold text-orange-600">⚠ Solo quedan {{ product.stock || 0 }} disponibles</p>

      <button type="button" class="btn-primary w-full" @click="handleAddToCart">Agregar al carrito</button>
    </div>
  </article>
</template>

<script setup>
import { computed, ref } from 'vue';
import api from '../../services/api';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['add-to-cart', 'quick-view', 'wishlist-change']);

const titleId = `product-title-${Math.random().toString(36).slice(2)}`;
const inWishlist = ref(false);

const discountPercentage = computed(() => Number(props.product.discount_percentage || 0));
const ratingValue = computed(() => Number(props.product.rating || 0));
const showLowStock = computed(() => Boolean(props.product.low_stock) || Number(props.product.stock || 0) < 5);
const starString = computed(() => {
  const full = Math.round(ratingValue.value);
  return `${'★'.repeat(full)}${'☆'.repeat(Math.max(0, 5 - full))}`;
});

function formatPrice(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
}

function openProduct() {
  window.location.href = `/products/${props.product.slug}`;
}

function openSeller() {
  if (!props.product.seller?.id) return;
  window.location.href = `/marketplace/vendors/${props.product.seller.id}`;
}

function handleAddToCart() {
  emit('add-to-cart', props.product);
}

async function toggleWishlist() {
  const currently = inWishlist.value;

  try {
    if (currently) {
      await api.delete(`/wishlist/${props.product.id}`);
    } else {
      await api.post(`/wishlist/${props.product.id}`);
    }

    inWishlist.value = !currently;
    emit('wishlist-change', { product: props.product, inWishlist: inWishlist.value });
  } catch {
    // keep last stable state
  }
}
</script>

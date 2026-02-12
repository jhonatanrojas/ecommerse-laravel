<template>
  <article class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
    <a :href="`/products/${product.slug}`" class="block">
      <div class="relative aspect-square overflow-hidden bg-gray-100">
        <img
          v-if="primaryImage"
          :src="primaryImage"
          :alt="product.name"
          class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
          loading="lazy"
        >
        <div v-else class="flex h-full items-center justify-center text-gray-300">
          <svg class="h-14 w-14" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 5H5a2 2 0 0 0-2 2v10c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Zm0 12H5V7h14v10Z"/>
          </svg>
        </div>

        <span
          v-if="isOutOfStock"
          class="absolute left-3 top-3 rounded-full bg-rose-500 px-3 py-1 text-xs font-semibold text-white"
        >
          Agotado
        </span>
      </div>
    </a>

    <div class="space-y-3 p-4">
      <h3 class="line-clamp-2 text-sm font-semibold text-gray-900 transition-colors group-hover:text-indigo-700 sm:text-base">
        {{ product.name }}
      </h3>

      <div class="flex items-center justify-between gap-2">
        <p class="text-lg font-bold text-gray-900">${{ formatPrice(product.price) }}</p>
        <p class="text-xs text-gray-500">Stock: {{ product.stock }}</p>
      </div>

      <button
        class="inline-flex w-full items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:bg-gray-300"
        :class="isOutOfStock ? 'bg-gray-300' : 'bg-indigo-600 hover:bg-indigo-700'"
        :disabled="isOutOfStock || adding"
        @click.prevent="$emit('add-to-cart', product)"
      >
        <span v-if="adding" class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
        <span v-else-if="justAdded">Añadido</span>
        <span v-else>Añadir al carrito</span>
      </button>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  adding: {
    type: Boolean,
    default: false,
  },
  justAdded: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['add-to-cart']);

const primaryImage = computed(() => {
  const primary = props.product.images?.find((image) => image.isPrimary);
  return primary?.url || props.product.images?.[0]?.url || null;
});

const isOutOfStock = computed(() => Number(props.product.stock) <= 0);

function formatPrice(value) {
  const amount = Number(value || 0);
  return amount.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>

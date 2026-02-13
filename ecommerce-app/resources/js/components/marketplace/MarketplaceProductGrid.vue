<template>
  <div>
    <div v-if="loading && !products.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
      <div v-for="n in 12" :key="n" class="mp-card overflow-hidden p-0">
        <div class="aspect-square mp-skeleton" />
        <div class="space-y-2 p-3">
          <div class="h-4 mp-skeleton" />
          <div class="h-6 w-2/3 mp-skeleton" />
          <div class="h-3 w-1/2 mp-skeleton" />
        </div>
      </div>
    </div>

    <div
      v-else
      :class="viewMode === 'list'
        ? 'space-y-4'
        : 'grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6'"
    >
      <component
        :is="viewMode === 'list' ? ProductCardMarketplace : MarketplaceProductCard"
        v-for="product in products"
        :key="product.id"
        :product="product"
      />
    </div>

    <div ref="sentinel" class="h-8" />

    <div v-if="loadingMore" class="py-4 text-center text-sm text-gray-500">Cargando más productos...</div>
    <div v-if="!hasMore && !loading && products.length" class="py-6 text-center text-sm font-medium text-gray-500">
      No hay más productos
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue';
import MarketplaceProductCard from './MarketplaceProductCard.vue';
import ProductCardMarketplace from './ProductCardMarketplace.vue';
import { useInfiniteScroll } from '../../composables/useInfiniteScroll';

const props = defineProps({
  products: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  loadingMore: {
    type: Boolean,
    default: false,
  },
  hasMore: {
    type: Boolean,
    default: false,
  },
  viewMode: {
    type: String,
    default: 'grid',
  },
});

const emit = defineEmits(['load-more']);

const { sentinel, hasMore, isLoading } = useInfiniteScroll(
  async () => {
    if (props.hasMore && !props.loadingMore) {
      emit('load-more');
    }
    return { hasMore: props.hasMore };
  },
  { threshold: 300, initialLoad: false },
);

watch(
  () => props.hasMore,
  (value) => {
    hasMore.value = value;
  },
  { immediate: true },
);

watch(
  () => props.loadingMore,
  (value) => {
    isLoading.value = value;
  },
  { immediate: true },
);
</script>

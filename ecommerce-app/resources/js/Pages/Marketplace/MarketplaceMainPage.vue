<template>
  <div>
    <MarketplaceHeader
      :results="store.pagination.total"
      :query="query"
      :sort="sort"
      :view-mode="viewMode"
      @sort-change="onSortChange"
      @view-change="onViewChange"
    />

    <div class="container mx-auto px-4 py-5">
      <div class="grid grid-cols-1 gap-5 lg:grid-cols-[300px_1fr]">
        <FilterSidebar
          class="hidden lg:block"
          :categories="categories"
          @filters-applied="applyFilters"
        />

        <section class="space-y-3">
          <div class="mb-3 flex items-center justify-between lg:hidden">
            <h1 class="text-xl font-black text-gray-900">Resultados</h1>
            <button class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700" @click="mobileFiltersOpen = true">
              Filtros
            </button>
          </div>

          <MarketplaceProductGrid
            :products="store.products"
            :loading="store.loading"
            :loading-more="store.loadingMore"
            :has-more="store.hasMoreProducts"
            :view-mode="viewMode"
            @load-more="loadMore"
          />
        </section>
      </div>
    </div>

    <MobileFilters
      :open="mobileFiltersOpen"
      :results="store.pagination.total"
      :categories="categories"
      @close="mobileFiltersOpen = false"
      @filters-applied="applyFilters"
    />
  </div>
</template>

<script setup>
import { computed, defineAsyncComponent, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import FilterSidebar from '../../components/marketplace/FilterSidebar.vue';
import MarketplaceHeader from '../../components/marketplace/MarketplaceHeader.vue';
import MarketplaceProductGrid from '../../components/marketplace/MarketplaceProductGrid.vue';
import { useMarketplaceStore } from '../../stores/marketplace';
import { useKeyboardShortcuts } from '../../composables/useKeyboardShortcuts';

const MobileFilters = defineAsyncComponent(() => import('../../components/marketplace/MobileFilters.vue'));

const store = useMarketplaceStore();
const route = useRoute();
const router = useRouter();
const viewMode = ref(route.query.view === 'list' ? 'list' : 'grid');
const sort = ref(route.query.sort ? String(route.query.sort) : 'relevance');
const mobileFiltersOpen = ref(false);

const query = computed(() => route.query.q || '');

const categories = computed(() => {
  const map = new Map();
  store.products.forEach((product) => {
    if (product.category?.id) {
      map.set(product.category.id, product.category);
    }
  });
  return [...map.values()];
});

onMounted(async () => {
  await store.fetchVendors();
  if (route.query.q) {
    await store.searchProducts(route.query.q, { page: 1, sort: sort.value });
  } else {
    await store.fetchMarketplaceProducts({ page: 1, sort: sort.value });
  }
});

async function onSearch(q) {
  router.push({ path: '/marketplace/search', query: { q } });
  await store.searchProducts(q, { page: 1, sort: sort.value });
}

async function applyFilters(filters) {
  mobileFiltersOpen.value = false;
  if (route.path.includes('/search') && query.value) {
    await store.searchProducts(query.value, { ...filters, sort: sort.value });
    return;
  }
  await store.fetchMarketplaceProducts({ ...filters, sort: sort.value }, false);
}

async function loadMore() {
  if (!store.hasMoreProducts || store.loadingMore) return;
  const next = store.pagination.current_page + 1;
  await store.fetchMarketplaceProducts({ ...store.filters, page: next, sort: sort.value }, true);
}

function onSortChange(value) {
  sort.value = value;
  applyFilters(store.filters);
}

function onViewChange(value) {
  viewMode.value = value;
  router.push({ query: { ...route.query, view: value } });
}

useKeyboardShortcuts({
  onOpenFilters: () => {
    mobileFiltersOpen.value = true;
  },
  onEscape: () => {
    mobileFiltersOpen.value = false;
  },
});
</script>

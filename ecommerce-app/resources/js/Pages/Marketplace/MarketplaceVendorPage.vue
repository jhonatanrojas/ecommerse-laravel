<template>
  <div class="container mx-auto px-4 py-5">
    <MarketplaceVendorHeader :vendor="store.currentVendor" />

    <section class="mt-5">
      <h2 class="mb-3 text-lg font-black">Productos del vendedor</h2>
      <MarketplaceProductGrid :products="store.vendorProducts" :loading="store.loading" :loading-more="false" :has-more="false" />
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import MarketplaceProductGrid from '../../components/marketplace/MarketplaceProductGrid.vue';
import MarketplaceVendorHeader from '../../components/marketplace/MarketplaceVendorHeader.vue';
import { useMarketplaceStore } from '../../stores/marketplace';

const route = useRoute();
const store = useMarketplaceStore();

onMounted(async () => {
  await Promise.all([
    store.fetchVendor(route.params.slug),
    store.fetchVendorProducts(route.params.slug),
  ]);
});
</script>

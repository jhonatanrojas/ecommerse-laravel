<template>
  <div>
    <div v-if="loading" class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
      <div
        v-for="n in 8"
        :key="`product-skeleton-${n}`"
        class="overflow-hidden rounded-2xl border border-gray-100 bg-white p-3"
      >
        <div class="skeleton aspect-square w-full rounded-xl" />
        <div class="mt-4 space-y-2">
          <div class="skeleton h-4 w-11/12" />
          <div class="skeleton h-4 w-7/12" />
          <div class="skeleton h-9 w-full rounded-xl" />
        </div>
      </div>
    </div>

    <div v-else-if="products.length" class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
        :adding="addingProductId === product.id"
        :just-added="justAddedProductId === product.id"
        @add-to-cart="$emit('add-to-cart', $event)"
      />
    </div>

    <div v-else class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
      <p class="text-sm text-gray-500">No se encontraron productos para esta categoría.</p>
    </div>
  </div>
</template>

<script setup>
import ProductCard from './ProductCard.vue';

defineProps({
  products: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  addingProductId: {
    type: [Number, null],
    default: null,
  },
  justAddedProductId: {
    type: [Number, null],
    default: null,
  },
});

defineEmits(['add-to-cart']);
</script>

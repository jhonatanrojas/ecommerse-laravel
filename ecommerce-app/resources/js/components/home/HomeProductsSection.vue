<template>
  <section class="section-padding bg-gradient-to-b from-white to-gray-50/70">
    <div class="container mx-auto px-4">
      <header class="mb-8 flex flex-col gap-3 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Catalogo</p>
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">Productos destacados para ti</h2>
        <p class="mx-auto max-w-2xl text-sm text-gray-500 md:text-base">
          Explora el catalogo completo, filtra por categoria o precio y agrega productos al carrito sin salir del Home.
        </p>
      </header>

      <ProductFilters
        :filters="filters"
        :categories="categories"
        :loading="loading"
        @change="handleFilterChange"
        @reset="handleResetFilters"
      />

      <div class="mt-6">
        <div v-if="error" class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
          {{ error }}
          <button class="ml-3 font-semibold underline" @click="refreshProducts">Reintentar</button>
        </div>

        <ProductGrid
          :products="products"
          :loading="loading"
          :adding-product-id="addingProductId"
          @add-to-cart="handleAddToCart"
        />

        <Pagination
          :meta="meta"
          :links="links"
          :loading="loading"
          @page-change="handlePageChange"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { storeToRefs } from 'pinia';
import ProductFilters from './products/ProductFilters.vue';
import ProductGrid from './products/ProductGrid.vue';
import Pagination from './products/Pagination.vue';
import { useProductsStore } from '../../stores/products';
import { useCartStore } from '../../stores/cart';

const emit = defineEmits(['show-toast']);

const productsStore = useProductsStore();
const cartStore = useCartStore();
const addingProductId = ref(null);

const { products, categories, meta, links, loading, error, filters } = storeToRefs(productsStore);

onMounted(async () => {
  await Promise.all([
    productsStore.fetchProducts(),
    productsStore.fetchCategories(),
  ]);
});

async function refreshProducts() {
  await productsStore.fetchProducts();
}

async function handleFilterChange(newFilters) {
  productsStore.updateFilters(newFilters);
  await productsStore.fetchProducts();
}

async function handleResetFilters() {
  productsStore.resetFilters();
  await productsStore.fetchProducts();
}

async function handlePageChange(page) {
  productsStore.updateFilters({ page });
  await productsStore.fetchProducts({ page });
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function handleAddToCart(product) {
  if (Number(product.stock) <= 0) {
    emit('show-toast', 'error', 'Este producto no tiene stock disponible.', 'Sin stock');
    return;
  }

  addingProductId.value = product.id;

  const availableVariant = product.variants?.find((variant) => Number(variant.stock) > 0) || null;
  const result = await cartStore.addItem(product.id, availableVariant?.id ?? null, 1);

  addingProductId.value = null;

  if (result.success) {
    emit('show-toast', 'success', `${product.name} fue añadido al carrito.`, 'Producto agregado');
  } else {
    emit('show-toast', 'error', result.error || 'No se pudo agregar el producto al carrito.', 'Error');
  }
}
</script>

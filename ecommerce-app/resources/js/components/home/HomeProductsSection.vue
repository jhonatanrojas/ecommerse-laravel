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

      <div class="md:sticky md:top-[76px] md:z-20 rounded-2xl md:bg-white/80 md:backdrop-blur-sm">
        <ProductFilters
          :filters="filters"
          :categories="categories"
          :loading="loading"
          @change="handleFilterChange"
          @reset="handleResetFilters"
        />
      </div>

      <div class="mt-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm text-gray-500">
            <span class="font-semibold text-gray-800">{{ totalResults }}</span>
            resultados
          </p>

          <div v-if="activeFilterChips.length" class="flex flex-wrap items-center gap-2">
            <button
              v-for="chip in activeFilterChips"
              :key="chip.key"
              class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
              @click="removeChip(chip.key)"
            >
              {{ chip.label }}
              <span class="text-gray-400">×</span>
            </button>
          </div>
        </div>

        <div v-if="error" class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
          {{ error }}
          <button class="ml-3 font-semibold underline" @click="refreshProducts">Reintentar</button>
        </div>

        <ProductGrid
          :products="products"
          :loading="loading"
          :adding-product-id="addingProductId"
          :just-added-product-id="justAddedProductId"
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
import { computed, onMounted, ref } from 'vue';
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
const justAddedProductId = ref(null);

const { products, categories, meta, links, loading, error, filters } = storeToRefs(productsStore);
const totalResults = computed(() => Number(meta.value?.total || 0));

const activeFilterChips = computed(() => {
  const chips = [];

  if (filters.value.search?.trim()) {
    chips.push({ key: 'search', label: `Buscar: ${filters.value.search}` });
  }

  if (filters.value.category_id) {
    const category = categories.value.find((item) => String(item.id) === String(filters.value.category_id));
    chips.push({
      key: 'category_id',
      label: `Categoría: ${category?.name || filters.value.category_id}`,
    });
  }

  if (filters.value.min_price !== '' && filters.value.min_price !== null) {
    chips.push({ key: 'min_price', label: `Min: $${filters.value.min_price}` });
  }

  if (filters.value.max_price !== '' && filters.value.max_price !== null) {
    chips.push({ key: 'max_price', label: `Max: $${filters.value.max_price}` });
  }

  if (filters.value.sort && filters.value.sort !== 'newest') {
    const labels = {
      price_asc: 'Precio ascendente',
      price_desc: 'Precio descendente',
    };
    chips.push({ key: 'sort', label: labels[filters.value.sort] || filters.value.sort });
  }

  return chips;
});

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
    justAddedProductId.value = product.id;
    setTimeout(() => {
      if (justAddedProductId.value === product.id) {
        justAddedProductId.value = null;
      }
    }, 1500);
    emit('show-toast', 'success', `${product.name} fue añadido al carrito.`, 'Producto agregado');
  } else {
    emit('show-toast', 'error', result.error || 'No se pudo agregar el producto al carrito.', 'Error');
  }
}

async function removeChip(key) {
  const patch = { page: 1 };

  if (key === 'sort') {
    patch.sort = 'newest';
  } else {
    patch[key] = '';
  }

  productsStore.updateFilters(patch);
  await productsStore.fetchProducts();
}
</script>

<template>
  <div class="min-h-screen bg-white text-gray-900">
    <header class="sticky top-0 z-50 bg-white/95 shadow-sm backdrop-blur-md">
      <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between gap-4">
          <a href="/" class="text-xl font-extrabold tracking-tight" style="color: var(--color-primary);">
            {{ appName }}
          </a>
          <div class="hidden lg:block">
            <Navigation location="header" variant="header" />
          </div>
          <div class="flex items-center gap-2">
            <a :href="accountUrl" class="rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900" aria-label="Mi cuenta">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </a>
            <CartButton />
          </div>
        </div>
      </div>
    </header>

    <main class="section-padding bg-gradient-to-b from-white to-gray-50/70">
      <div class="container mx-auto px-4">
        <div class="mb-4 text-sm text-gray-500">
          <a href="/" class="hover:text-indigo-600">Inicio</a>
          <span class="mx-2">/</span>
          <a href="/categories" class="hover:text-indigo-600">Categorías</a>
          <span v-if="category" class="mx-2">/</span>
          <span v-if="category" class="text-gray-700">{{ category.name }}</span>
        </div>

        <CategoryHeader
          :title="category?.name || 'Categoría'"
          :description="category?.description || ''"
        />

        <div class="mb-6 rounded-2xl bg-white/80 backdrop-blur-sm md:sticky md:top-[76px] md:z-20">
          <CategoryFilters
            :filters="filters"
            :loading="loading"
            @change="handleFilterChange"
            @reset="handleResetFilters"
          />
        </div>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm text-gray-500">
            <span class="font-semibold text-gray-800">{{ pagination.total }}</span>
            resultados
          </p>
        </div>

        <div v-if="error" class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
          {{ error }}
          <button class="ml-3 font-semibold underline" @click="fetchProducts">Reintentar</button>
        </div>

        <ProductGrid
          :products="products"
          :loading="loading"
          :adding-product-id="addingProductId"
          :just-added-product-id="justAddedProductId"
          @add-to-cart="handleAddToCart"
        />

        <Pagination
          :pagination="pagination"
          :links="links"
          :loading="loading"
          @page-change="handlePageChange"
        />
      </div>
    </main>

    <footer class="bg-gray-900 text-white">
      <div class="container mx-auto px-4 py-10 md:py-12">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
          <div>
            <p class="text-lg font-bold">{{ appName }}</p>
            <p class="mt-2 text-sm text-gray-400">Compra por categorías con filtros y paginación.</p>
          </div>
          <div>
            <p class="mb-3 text-sm font-bold uppercase tracking-wider text-white">Navegación</p>
            <Navigation location="footer" variant="footer" />
          </div>
          <div>
            <p class="mb-3 text-sm font-bold uppercase tracking-wider text-white">Soporte</p>
            <p class="text-sm text-gray-400">info@tutienda.com</p>
            <p class="text-sm text-gray-400">(123) 456-7890</p>
          </div>
        </div>
      </div>
    </footer>

    <CartDrawer />

    <CartToast
      :show="toast.show"
      :type="toast.type"
      :title="toast.title"
      :message="toast.message"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { storeToRefs } from 'pinia';
import Navigation from '../Components/Navigation.vue';
import CategoryHeader from '../components/categories/CategoryHeader.vue';
import CategoryFilters from '../components/categories/CategoryFilters.vue';
import ProductGrid from '../components/categories/products/ProductGrid.vue';
import Pagination from '../components/categories/Pagination.vue';
import CartButton from '../components/cart/CartButton.vue';
import CartDrawer from '../components/cart/CartDrawer.vue';
import CartToast from '../components/cart/CartToast.vue';
import { useAuthStore } from '../stores/auth';
import { useCartStore } from '../stores/cart';
import { useCategoriesStore } from '../stores/categories';

const appName = 'Mi Tienda';
const authStore = useAuthStore();
const cartStore = useCartStore();
const categoriesStore = useCategoriesStore();
const slug = window.location.pathname.split('/').filter(Boolean).pop();

const addingProductId = ref(null);
const justAddedProductId = ref(null);

const toast = reactive({
  show: false,
  type: 'info',
  title: '',
  message: '',
});

const { products, currentCategory, pagination, links, loadingProducts, errors, productFilters } = storeToRefs(categoriesStore);

const accountUrl = computed(() => (authStore.isAuthenticated ? '/customer' : '/login'));
const category = computed(() => currentCategory.value);
const loading = computed(() => loadingProducts.value);
const error = computed(() => errors.value.products);
const filters = computed(() => productFilters.value);

function hydrateFiltersFromQuery() {
  const params = new URLSearchParams(window.location.search);
  const patch = {};

  if (params.has('min_price')) patch.min_price = params.get('min_price') ?? '';
  if (params.has('max_price')) patch.max_price = params.get('max_price') ?? '';
  if (params.has('sort')) patch.sort = params.get('sort') ?? 'newest';
  if (params.has('page')) patch.page = Number(params.get('page') || 1);

  if (Object.keys(patch).length > 0) {
    categoriesStore.updateProductFilters(patch);
  }
}

function syncQueryString() {
  const params = new URLSearchParams();
  const current = filters.value;

  if (current.min_price !== '' && current.min_price !== null) {
    params.set('min_price', String(current.min_price));
  }

  if (current.max_price !== '' && current.max_price !== null) {
    params.set('max_price', String(current.max_price));
  }

  if (current.sort && current.sort !== 'newest') {
    params.set('sort', current.sort);
  }

  if (Number(current.page || 1) > 1) {
    params.set('page', String(current.page));
  }

  const query = params.toString();
  const basePath = window.location.pathname;
  const nextUrl = query ? `${basePath}?${query}` : basePath;

  window.history.replaceState({}, '', nextUrl);
}

function showToast(type, message, title = '') {
  toast.show = true;
  toast.type = type;
  toast.message = message;
  toast.title = title;
}

async function fetchProducts() {
  await categoriesStore.fetchCategoryProducts(slug);
  syncQueryString();
}

async function handleFilterChange(newFilters) {
  categoriesStore.updateProductFilters(newFilters);
  await categoriesStore.fetchCategoryProducts(slug);
  syncQueryString();
}

async function handleResetFilters() {
  categoriesStore.resetProductFilters();
  await categoriesStore.fetchCategoryProducts(slug);
  syncQueryString();
}

async function handlePageChange(page) {
  categoriesStore.updateProductFilters({ page });
  await categoriesStore.fetchCategoryProducts(slug, { page });
  syncQueryString();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function handleAddToCart(product) {
  if (Number(product.stock) <= 0) {
    showToast('error', 'Este producto no tiene stock disponible.', 'Sin stock');
    return;
  }

  addingProductId.value = product.id;
  const variant = product.variants?.find((item) => Number(item.stock) > 0) || null;
  const result = await cartStore.addItem(product.id, variant?.id ?? null, 1);
  addingProductId.value = null;

  if (result.success) {
    justAddedProductId.value = product.id;
    setTimeout(() => {
      if (justAddedProductId.value === product.id) {
        justAddedProductId.value = null;
      }
    }, 1500);

    showToast('success', `${product.name} fue añadido al carrito.`, 'Producto agregado');
  } else {
    showToast('error', result.error || 'No se pudo agregar el producto al carrito.', 'Error');
  }
}

onMounted(async () => {
  hydrateFiltersFromQuery();

  await Promise.all([
    cartStore.fetchCart(),
    authStore.checkAuth(),
  ]);

  await fetchProducts();
});
</script>

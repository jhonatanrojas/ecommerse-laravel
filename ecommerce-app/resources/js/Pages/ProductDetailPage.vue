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

    <main class="section-padding bg-gradient-to-b from-white to-gray-50/70 pb-24 md:pb-0">
      <div class="container mx-auto px-4">
        <div class="mb-6 text-sm text-gray-500">
          <a href="/" class="hover:text-indigo-600">Inicio</a>
          <span class="mx-2">/</span>
          <a href="/products" class="hover:text-indigo-600">Tienda</a>
          <span v-if="product" class="mx-2">/</span>
          <span v-if="product" class="text-gray-700">{{ product.name }}</span>
        </div>

        <div v-if="loading" class="grid grid-cols-1 gap-8 lg:grid-cols-2">
          <div class="skeleton h-[460px] w-full rounded-2xl" />
          <div class="space-y-4">
            <div class="skeleton h-4 w-28" />
            <div class="skeleton h-8 w-10/12" />
            <div class="skeleton h-6 w-40" />
            <div class="skeleton h-28 w-full" />
            <div class="skeleton h-11 w-56" />
          </div>
        </div>

        <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
          {{ error }}
          <button class="ml-3 font-semibold underline" @click="fetchProduct">Reintentar</button>
        </div>

        <div v-else-if="product" class="grid grid-cols-1 gap-10 lg:grid-cols-2">
          <section>
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
              <img
                v-if="activeImage"
                :src="activeImage"
                :alt="product.name"
                class="h-[460px] w-full object-cover"
              >
              <div v-else class="flex h-[460px] items-center justify-center bg-gray-100 text-gray-300">
                <svg class="h-16 w-16" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M19 5H5a2 2 0 0 0-2 2v10c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Zm0 12H5V7h14v10Z"/>
                </svg>
              </div>
            </div>

            <div v-if="product.images.length > 1" class="mt-3 grid grid-cols-5 gap-2">
              <button
                v-for="image in product.images"
                :key="image.id"
                class="overflow-hidden rounded-xl border transition"
                :class="image.url === activeImage ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-gray-200 hover:border-indigo-300'"
                @click="activeImage = image.url"
              >
                <img :src="image.url" :alt="image.altText || product.name" class="h-20 w-full object-cover">
              </button>
            </div>
          </section>

          <section>
            <p v-if="product.category?.name" class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">
              {{ product.category.name }}
            </p>

            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">{{ product.name }}</h1>

            <div class="mt-4 flex items-center gap-3">
              <p class="text-3xl font-extrabold text-gray-900">${{ formatPrice(currentPrice) }}</p>
              <span v-if="isOutOfStock" class="rounded-full bg-rose-500 px-3 py-1 text-xs font-semibold text-white">
                Agotado
              </span>
              <span v-else class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                Stock {{ currentStock }}
              </span>
            </div>

            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-gray-600">{{ product.description || 'Sin descripción disponible.' }}</p>

            <div v-if="product.variants.length" class="mt-6">
              <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Variante</label>
              <select
                v-model="selectedVariantId"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
              >
                <option :value="null">Selecciona una variante</option>
                <option
                  v-for="variant in product.variants"
                  :key="variant.id"
                  :value="variant.id"
                  :disabled="variant.stock <= 0"
                >
                  {{ variant.name }} - ${{ formatPrice(variant.price || product.price) }}
                  <span v-if="variant.stock <= 0">(Agotada)</span>
                </option>
              </select>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
              <button
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-gray-300"
                :disabled="isOutOfStock || addingToCart"
                @click="addToCart"
              >
                <span v-if="addingToCart" class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                <span v-else-if="justAddedMain">Añadido al carrito</span>
                <span v-else>Añadir al carrito</span>
              </button>

              <a href="/products" class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                Volver a tienda
              </a>
            </div>
          </section>
        </div>

        <section v-if="relatedProducts.length" class="mt-14">
          <div class="mb-5 flex items-end justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Sugerencias</p>
              <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-gray-900">Productos relacionados</h2>
            </div>
            <a href="/products" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Ver todo</a>
          </div>

          <ProductGrid
            :products="relatedProducts"
            :loading="false"
            :adding-product-id="addingRelatedProductId"
            :just-added-product-id="justAddedRelatedProductId"
            @add-to-cart="addRelatedToCart"
          />
        </section>
      </div>
    </main>

    <div
      v-if="product && !loading"
      class="fixed inset-x-0 bottom-0 z-[120] border-t border-gray-200 bg-white p-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] shadow-lg md:hidden"
    >
      <div class="mx-auto flex max-w-3xl items-center gap-2">
        <button
          class="inline-flex min-h-[44px] flex-1 items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700"
          @click="contactSeller"
        >
          Contactar vendedor
        </button>
        <button
          class="inline-flex min-h-[44px] flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-gray-300"
          :disabled="isOutOfStock || addingToCart"
          @click="buyNowMobile"
        >
          Comprar ahora
        </button>
      </div>
    </div>

    <footer class="bg-gray-900 text-white">
      <div class="container mx-auto px-4 py-10 md:py-12">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
          <div>
            <p class="text-lg font-bold">{{ appName }}</p>
            <p class="mt-2 text-sm text-gray-400">Detalle de producto con experiencia consistente al Home.</p>
          </div>
          <div>
            <p class="mb-3 text-sm font-bold uppercase tracking-wider text-white">Navegacion</p>
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
import api from '../services/api';
import Navigation from '../Components/Navigation.vue';
import CartButton from '../components/cart/CartButton.vue';
import CartDrawer from '../components/cart/CartDrawer.vue';
import CartToast from '../components/cart/CartToast.vue';
import ProductGrid from '../components/home/products/ProductGrid.vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';

const appName = 'Mi Tienda';
const cartStore = useCartStore();
const authStore = useAuthStore();

const product = ref(null);
const relatedProducts = ref([]);
const activeImage = ref(null);
const selectedVariantId = ref(null);
const loading = ref(true);
const error = ref(null);
const addingToCart = ref(false);
const addingRelatedProductId = ref(null);
const justAddedMain = ref(false);
const justAddedRelatedProductId = ref(null);

const toast = reactive({
  show: false,
  type: 'info',
  title: '',
  message: '',
});

const accountUrl = computed(() => (authStore.isAuthenticated ? '/customer' : '/login'));
const slug = computed(() => window.location.pathname.split('/').filter(Boolean).pop());

const selectedVariant = computed(() => {
  if (!product.value?.variants?.length || !selectedVariantId.value) return null;
  return product.value.variants.find((variant) => variant.id === selectedVariantId.value) || null;
});

const currentPrice = computed(() => Number(selectedVariant.value?.price || product.value?.price || 0));
const currentStock = computed(() => Number(selectedVariant.value?.stock ?? product.value?.stock ?? 0));
const isOutOfStock = computed(() => currentStock.value <= 0);

function normalizeProduct(raw = {}) {
  const category = raw.category ?? raw.categoria ?? null;
  const images = raw.images ?? raw.imagenes ?? [];
  const variants = raw.variants ?? raw.variantes ?? [];

  return {
    id: raw.id,
    name: raw.name ?? raw.nombre ?? '',
    slug: raw.slug ?? '',
    description: raw.description ?? raw.descripcion ?? '',
    price: Number(raw.price ?? raw.precio ?? 0),
    stock: Number(raw.stock ?? 0),
    status: raw.status ?? raw.estado ?? 'active',
    category: category
      ? { id: category.id, name: category.name ?? category.nombre ?? '', slug: category.slug ?? '' }
      : null,
    images: images.map((image) => ({
      id: image.id,
      url: image.url,
      thumbnailUrl: image.thumbnail_url ?? null,
      altText: image.alt_text ?? image.alt ?? null,
      isPrimary: Boolean(image.is_primary),
      order: image.order ?? image.orden ?? 0,
    })),
    variants: variants.map((variant) => ({
      id: variant.id,
      name: variant.name ?? variant.nombre ?? '',
      sku: variant.sku ?? null,
      price: Number(variant.price ?? variant.precio ?? 0),
      stock: Number(variant.stock ?? 0),
      attributes: variant.attributes ?? variant.atributos ?? {},
    })),
  };
}

function formatPrice(value) {
  const amount = Number(value || 0);
  return amount.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function showToast(type, message, title = '') {
  toast.show = true;
  toast.type = type;
  toast.message = message;
  toast.title = title;
}

async function fetchProduct() {
  loading.value = true;
  error.value = null;

  try {
    const response = await api.get(`/products/${slug.value}`);
    product.value = normalizeProduct(response.data?.data || {});

    const primary = product.value.images.find((image) => image.isPrimary)?.url;
    activeImage.value = primary || product.value.images[0]?.url || null;

    if (product.value.variants.length === 1 && product.value.variants[0].stock > 0) {
      selectedVariantId.value = product.value.variants[0].id;
    }

    await fetchRelated();
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'Producto no encontrado.';
    } else {
      error.value = 'No se pudo cargar el producto.';
    }
  } finally {
    loading.value = false;
  }
}

async function fetchRelated() {
  if (!product.value?.slug) {
    relatedProducts.value = [];
    return;
  }

  try {
    const response = await api.get(`/products/${product.value.slug}/related`, {
      params: { limit: 4 },
    });

    relatedProducts.value = (response.data?.data || []).map(normalizeProduct);
  } catch {
    relatedProducts.value = [];
  }
}

async function addToCart() {
  if (!product.value) return;

  if (isOutOfStock.value) {
    showToast('error', 'Este producto no tiene stock disponible.', 'Sin stock');
    return;
  }

  addingToCart.value = true;
  const result = await cartStore.addItem(product.value.id, selectedVariantId.value, 1);
  addingToCart.value = false;

  if (result.success) {
    justAddedMain.value = true;
    setTimeout(() => {
      justAddedMain.value = false;
    }, 1700);
    showToast('success', `${product.value.name} fue añadido al carrito.`, 'Producto agregado');
  } else {
    showToast('error', result.error || 'No se pudo agregar el producto al carrito.', 'Error');
  }
}

function contactSeller() {
  if (!product.value?.id) return;
  window.location.href = `/marketplace/products/${product.value.slug}`;
}

async function buyNowMobile() {
  await addToCart();
  if (!isOutOfStock.value) {
    window.location.href = '/checkout';
  }
}

async function addRelatedToCart(relatedProduct) {
  addingRelatedProductId.value = relatedProduct.id;
  const variant = relatedProduct.variants?.find((item) => Number(item.stock) > 0) || null;
  const result = await cartStore.addItem(relatedProduct.id, variant?.id ?? null, 1);
  addingRelatedProductId.value = null;

  if (result.success) {
    justAddedRelatedProductId.value = relatedProduct.id;
    setTimeout(() => {
      if (justAddedRelatedProductId.value === relatedProduct.id) {
        justAddedRelatedProductId.value = null;
      }
    }, 1500);
    showToast('success', `${relatedProduct.name} fue añadido al carrito.`, 'Producto agregado');
  } else {
    showToast('error', result.error || 'No se pudo agregar el producto al carrito.', 'Error');
  }
}

onMounted(async () => {
  await Promise.all([
    cartStore.fetchCart(),
    authStore.checkAuth(),
  ]);

  await fetchProduct();
});
</script>

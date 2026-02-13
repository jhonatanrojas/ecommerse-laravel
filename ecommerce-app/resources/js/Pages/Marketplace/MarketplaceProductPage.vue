<template>
  <div class="container mx-auto px-4 py-5">
    <div v-if="store.loading" class="grid grid-cols-1 gap-6 lg:grid-cols-[1.3fr_1fr]">
      <div class="mp-skeleton h-[460px]" />
      <div class="space-y-3">
        <div class="mp-skeleton h-6 w-2/3" />
        <div class="mp-skeleton h-12 w-1/2" />
        <div class="mp-skeleton h-24" />
      </div>
    </div>

    <div v-else-if="store.currentProduct" class="space-y-6">
      <nav class="text-xs text-gray-500">
        <a href="/" class="hover:text-gray-700">Inicio</a>
        <span class="mx-1">›</span>
        <a href="/marketplace" class="hover:text-gray-700">Marketplace</a>
        <span class="mx-1">›</span>
        <span class="text-gray-700">{{ store.currentProduct.name }}</span>
      </nav>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.3fr_1fr]">
        <MarketplaceProductGallery :images="store.currentProduct.images" :alt="store.currentProduct.name" />

        <section class="mp-card p-5">
          <h1 class="text-2xl font-black tracking-tight text-gray-900">{{ store.currentProduct.name }}</h1>

          <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
            <span class="text-amber-500">{{ stars(store.currentProduct.rating) }}</span>
            <span>{{ Number(store.currentProduct.rating || 0).toFixed(1) }}</span>
            <span>•</span>
            <span>{{ store.currentProduct.reviews_count || 0 }} opiniones</span>
          </div>

          <div class="mt-4 space-y-1">
            <p v-if="store.currentProduct.compare_price" class="text-sm text-gray-400 line-through">{{ formatUsd(store.currentProduct.compare_price) }}</p>
            <p class="mp-price text-5xl">{{ formatUsd(store.currentProduct.price) }}</p>
            <p class="text-sm text-gray-500">{{ formatBs(store.currentProduct.price) }}</p>
          </div>

          <div class="mt-4 space-y-1 text-sm text-gray-700">
            <p>📦 Stock disponible: <strong>{{ store.currentProduct.stock || 0 }}</strong></p>
            <p>🚚 {{ store.currentProduct.free_shipping ? 'Envio gratis' : 'Envio nacional' }} · {{ store.currentProduct.estimated_delivery || '2-5 dias habiles' }}</p>
            <p>📍 Ubicacion del vendedor: {{ store.currentProduct.seller?.location || store.currentProduct.vendor?.location || 'No especificada' }}</p>
            <p>⭐ Calificacion del vendedor: {{ Number(store.currentProduct.seller?.rating || store.currentProduct.seller_rating || 0).toFixed(1) }} / 5</p>
            <p>👁 {{ store.currentProduct.views_count || 0 }} vistas del producto</p>
          </div>

          <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-3">
            <button class="mp-btn-primary w-full" :disabled="store.orderProcessing" @click="buyNow">
              {{ store.orderProcessing ? 'Procesando...' : 'Comprar ahora' }}
            </button>
            <button class="mp-btn-secondary w-full" @click="contactSeller">Contactar al vendedor</button>
            <button class="mp-btn-secondary w-full" @click="toggleFavorite">
              {{ isFavorite ? 'En favoritos' : 'Favoritos' }}
            </button>
          </div>

          <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm">
            <p class="font-bold text-gray-800">Vendido por</p>
            <router-link
              v-if="store.currentProduct.seller?.slug || store.currentProduct.vendor?.slug || store.currentProduct.vendor?.uuid"
              :to="`/marketplace/vendors/${store.currentProduct.seller?.slug || store.currentProduct.vendor?.slug || store.currentProduct.vendor?.uuid}`"
              class="text-[var(--mp-accent)] hover:underline"
            >
              {{ store.currentProduct.seller?.name || store.currentProduct.vendor?.business_name || 'Vendedor' }}
            </router-link>
            <p class="mt-1 text-gray-600">{{ store.currentProduct.description || 'Sin descripcion.' }}</p>
          </div>
        </section>
      </div>

      <MarketplaceQuestions
        :questions="store.questions"
        :loading="store.questionSubmitting"
        @submit-question="sendQuestion"
      />

      <MarketplaceReviews
        :reviews="store.reviews"
        :summary="store.reviewSummary"
        :loading="store.reviewsSubmitting"
        @submit-review="sendReview"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import MarketplaceProductGallery from '../../components/marketplace/MarketplaceProductGallery.vue';
import MarketplaceQuestions from '../../components/marketplace/MarketplaceQuestions.vue';
import MarketplaceReviews from '../../components/marketplace/MarketplaceReviews.vue';
import { useAuthStore } from '../../stores/auth';
import { useMarketplaceStore } from '../../stores/marketplace';

const route = useRoute();
const router = useRouter();
const store = useMarketplaceStore();
const authStore = useAuthStore();
const isFavorite = ref(false);

onMounted(async () => {
  await authStore.checkAuth();
  const product = await store.fetchProduct(route.params.slug);
  if (product?.id) {
    await Promise.all([
      store.fetchQuestions(product.id),
      store.fetchReviews(route.params.slug),
    ]);
  }
});

function formatUsd(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
}

function formatBs(value) {
  const amount = Number(value || 0) * 36.5;
  return new Intl.NumberFormat('es-VE', { style: 'currency', currency: 'VES', maximumFractionDigits: 2 }).format(amount);
}

function stars(value) {
  const full = Math.round(Number(value || 0));
  return `${'★'.repeat(full)}${'☆'.repeat(Math.max(0, 5 - full))}`;
}

async function buyNow() {
  const product = store.currentProduct;
  if (!product) return;

  const payload = await store.createOrder(
    product.id,
    null,
    authStore.user?.id || null,
  );

  if (payload?.order_uuid) {
    router.push(`/messages/${payload.order_uuid}`);
  }
}

function contactSeller() {
  const target = store.currentProduct?.seller?.slug || store.currentProduct?.vendor?.slug || store.currentProduct?.vendor?.uuid;
  if (!target) return;
  router.push(`/marketplace/vendors/${target}`);
}

function toggleFavorite() {
  isFavorite.value = !isFavorite.value;
}

async function sendQuestion(text) {
  const product = store.currentProduct;
  if (!product?.id) return;

  await store.submitQuestion(product.id, text);
}

async function sendReview(payload) {
  await store.submitReview(route.params.slug, payload);
}
</script>

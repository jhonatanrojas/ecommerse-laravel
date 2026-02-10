<template>
  <section class="section-padding" :class="renderedData.background === 'white' ? 'bg-white' : 'bg-gray-50'">
    <div class="container mx-auto px-4">
      <!-- Section Header -->
      <div class="section-header">
        <h2 class="section-title">{{ section.title }}</h2>
        <p v-if="section.subtitle" class="section-subtitle">{{ section.subtitle }}</p>
      </div>

      <!-- Products Grid -->
      <div
        v-if="products.length"
        :class="gridClass"
        class="gap-5 md:gap-6 stagger-children"
      >
        <div
          v-for="product in products"
          :key="product.id"
          class="product-card card overflow-hidden group animate-fade-in-up"
        >
          <a :href="`/products/${product.slug}`" class="block">
            <!-- Product Image Container -->
            <div class="aspect-square bg-gray-100 overflow-hidden relative">
              <img
                v-if="product.image"
                :src="product.image"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                loading="lazy"
                @error="handleImageError"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
              </div>

              <!-- Discount Badge -->
              <span v-if="getDiscount(product)" class="badge-discount">
                -{{ getDiscount(product) }}%
              </span>

              <!-- Quick Actions Overlay -->
              <div class="quick-actions rounded-b-none">
                <button
                  class="quick-action-btn"
                  title="Añadir al carrito"
                  @click.prevent="addToCart(product)"
                  aria-label="Añadir al carrito"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                  </svg>
                </button>
                <button
                  class="quick-action-btn"
                  title="Vista rápida"
                  @click.prevent
                  aria-label="Vista rápida"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                <button
                  class="quick-action-btn"
                  title="Añadir a favoritos"
                  @click.prevent
                  aria-label="Añadir a favoritos"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Product Info -->
            <div class="p-4">
              <!-- Category -->
              <p v-if="product.category" class="text-xs font-medium text-indigo-500 uppercase tracking-wider mb-1.5">
                {{ product.category }}
              </p>

              <!-- Product Name -->
              <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-2 line-clamp-2 leading-snug group-hover:text-indigo-600 transition-colors duration-200">
                {{ product.name }}
              </h3>

              <!-- Rating -->
              <div v-if="renderedData.show_rating && product.rating" class="flex items-center gap-1.5 mb-2">
                <div class="flex">
                  <svg
                    v-for="star in 5"
                    :key="star"
                    class="w-3.5 h-3.5"
                    :class="star <= Math.round(product.rating) ? 'text-amber-400 fill-current' : 'text-gray-200 fill-current'"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>
                <span class="text-xs text-gray-400">{{ product.rating.toFixed(1) }}</span>
              </div>

              <!-- Price -->
              <div v-if="renderedData.show_price" class="flex items-center gap-2">
                <span v-if="product.sale_price" class="text-base sm:text-lg font-bold text-red-600">
                  ${{ formatPrice(product.sale_price) }}
                </span>
                <span :class="product.sale_price ? 'text-sm text-gray-400 line-through' : 'text-base sm:text-lg font-bold text-gray-900'">
                  ${{ formatPrice(product.price) }}
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <p class="text-gray-400">No hay productos disponibles</p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'FeaturedProductsSection',
  props: {
    section: {
      type: Object,
      required: true,
    },
  },
  computed: {
    renderedData() {
      return this.section.rendered_data || {};
    },
    products() {
      return this.renderedData.products || [];
    },
    gridClass() {
      const columns = this.renderedData.columns || 4;
      const columnClasses = {
        2: 'grid grid-cols-2',
        3: 'grid grid-cols-2 lg:grid-cols-3',
        4: 'grid grid-cols-2 lg:grid-cols-4',
        5: 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5',
        6: 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6',
      };
      return columnClasses[columns] || columnClasses[4];
    },
  },
  methods: {
    formatPrice(price) {
      return parseFloat(price).toLocaleString('es-ES', { minimumFractionDigits: 2 });
    },
    getDiscount(product) {
      if (product.sale_price && product.price) {
        const discount = Math.round((1 - product.sale_price / product.price) * 100);
        return discount > 0 ? discount : null;
      }
      return null;
    },
    addToCart(product) {
      // Emit event or handle cart logic
      console.log('Add to cart:', product.name);
    },
    handleImageError(event) {
      event.target.style.display = 'none';
    },
  },
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

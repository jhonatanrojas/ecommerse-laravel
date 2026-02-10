<template>
  <section class="featured-products-section py-12 md:py-16 lg:py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <!-- Section Title -->
      <div class="text-center mb-8 md:mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          {{ section.title }}
        </h2>
      </div>

      <!-- Products Grid -->
      <div
        v-if="products.length"
        :class="gridClass"
        class="gap-4 md:gap-6"
      >
        <div
          v-for="product in products"
          :key="product.id"
          class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300"
        >
          <a :href="`/products/${product.slug}`" class="block">
            <!-- Product Image -->
            <div class="aspect-square bg-gray-200 overflow-hidden">
              <img
                v-if="product.image"
                :src="product.image"
                :alt="product.name"
                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                @error="handleImageError"
                loading="lazy"
              />
              <div
                v-else
                class="w-full h-full flex items-center justify-center text-gray-400"
              >
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    fill-rule="evenodd"
                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>

            <!-- Product Info -->
            <div class="p-3 sm:p-4">
              <!-- Category -->
              <p
                v-if="product.category"
                class="text-xs text-gray-500 uppercase tracking-wide mb-1"
              >
                {{ product.category }}
              </p>

              <!-- Product Name -->
              <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                {{ product.name }}
              </h3>

              <!-- Rating -->
              <div
                v-if="renderedData.show_rating && product.rating"
                class="flex items-center mb-2"
              >
                <div class="flex text-yellow-400">
                  <svg
                    v-for="star in 5"
                    :key="star"
                    class="w-4 h-4"
                    :class="star <= Math.round(product.rating) ? 'fill-current' : 'fill-gray-300'"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
                <span class="ml-2 text-sm text-gray-600">
                  {{ product.rating.toFixed(1) }}
                </span>
              </div>

              <!-- Price -->
              <div v-if="renderedData.show_price" class="flex items-center gap-2">
                <span
                  v-if="product.sale_price"
                  class="text-lg sm:text-xl font-bold text-red-600"
                >
                  ${{ formatPrice(product.sale_price) }}
                </span>
                <span
                  :class="product.sale_price ? 'text-sm text-gray-500 line-through' : 'text-lg sm:text-xl font-bold text-gray-900'"
                >
                  ${{ formatPrice(product.price) }}
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <p class="text-gray-500">No products available</p>
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
      const layout = this.renderedData.layout || 'grid';
      
      if (layout !== 'grid') {
        return 'flex overflow-x-auto';
      }
      
      const columnClasses = {
        2: 'grid grid-cols-1 sm:grid-cols-2',
        3: 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4: 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
        5: 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5',
        6: 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6',
      };
      
      return columnClasses[columns] || columnClasses[4];
    },
  },
  methods: {
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    handleImageError(event) {
      // Hide the broken image and show placeholder
      event.target.style.display = 'none';
      const placeholder = event.target.nextElementSibling;
      if (placeholder) {
        placeholder.style.display = 'flex';
      }
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

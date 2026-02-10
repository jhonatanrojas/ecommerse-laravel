<template>
  <section class="featured-categories-section py-12 md:py-16 lg:py-20 bg-white">
    <div class="container mx-auto px-4">
      <!-- Section Title -->
      <div class="text-center mb-8 md:mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          {{ section.title }}
        </h2>
      </div>

      <!-- Categories Grid -->
      <div
        v-if="categories.length"
        :class="gridClass"
        class="gap-4 md:gap-6"
      >
        <div
          v-for="category in categories"
          :key="category.id"
          class="category-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 group"
        >
          <a :href="`/categories/${category.slug}`" class="block">
            <!-- Category Image -->
            <div class="aspect-video bg-gray-200 overflow-hidden relative">
              <img
                v-if="category.image"
                :src="category.image"
                :alt="category.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                @error="handleImageError"
                loading="lazy"
              />
              <div
                v-else
                class="w-full h-full flex items-center justify-center text-gray-400"
              >
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                  />
                </svg>
              </div>

              <!-- Product Count Badge -->
              <div
                v-if="renderedData.show_product_count && category.product_count !== undefined"
                class="absolute top-2 right-2 sm:top-3 sm:right-3 bg-blue-600 text-white px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-semibold"
              >
                {{ category.product_count }} {{ category.product_count === 1 ? 'Product' : 'Products' }}
              </div>
            </div>

            <!-- Category Info -->
            <div class="p-3 sm:p-4">
              <!-- Category Name -->
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">
                {{ category.name }}
              </h3>

              <!-- Category Description -->
              <p
                v-if="category.description"
                class="text-xs sm:text-sm text-gray-600 line-clamp-2"
              >
                {{ category.description }}
              </p>

              <!-- View Link -->
              <div class="mt-2 sm:mt-3 flex items-center text-blue-600 text-sm sm:text-base font-medium group-hover:text-blue-700">
                <span>Shop Now</span>
                <svg
                  class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                  />
                </svg>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <p class="text-gray-500">No categories available</p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'FeaturedCategoriesSection',
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
    categories() {
      return this.renderedData.categories || [];
    },
    gridClass() {
      const columns = this.renderedData.columns || 3;
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
      
      return columnClasses[columns] || columnClasses[3];
    },
  },
  methods: {
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

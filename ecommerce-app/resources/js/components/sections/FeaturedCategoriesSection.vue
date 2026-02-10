<template>
  <section class="section-padding bg-white">
    <div class="container mx-auto px-4">
      <!-- Section Header -->
      <div class="section-header">
        <h2 class="section-title">{{ section.title }}</h2>
        <p v-if="section.subtitle" class="section-subtitle">{{ section.subtitle }}</p>
      </div>

      <!-- Scrollable Categories Bar -->
      <div
        v-if="categories.length"
        class="flex gap-4 md:gap-6 overflow-x-auto hide-scrollbar pb-4 md:justify-center md:flex-wrap"
      >
        <a
          v-for="(category, index) in categories"
          :key="category.id"
          :href="`/categories/${category.slug}`"
          class="flex-shrink-0 group animate-fade-in-up"
          :style="{ animationDelay: `${index * 80}ms` }"
        >
          <div class="flex flex-col items-center w-24 sm:w-28 md:w-32">
            <!-- Circular Image -->
            <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-2xl overflow-hidden bg-gray-100 mb-3 shadow-sm group-hover:shadow-lg group-hover:scale-105 transition-all duration-300 ring-2 ring-transparent group-hover:ring-indigo-200">
              <img
                v-if="category.image"
                :src="category.image"
                :alt="category.name"
                class="w-full h-full object-cover"
                loading="lazy"
                @error="handleImageError"
              />
              <div
                v-else
                class="w-full h-full flex items-center justify-center text-gray-400 bg-gradient-to-br from-gray-100 to-gray-200"
              >
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                </svg>
              </div>
            </div>

            <!-- Category Name -->
            <h3 class="text-xs sm:text-sm font-semibold text-gray-800 text-center leading-tight group-hover:text-indigo-600 transition-colors duration-200">
              {{ category.name }}
            </h3>

            <!-- Product Count -->
            <p
              v-if="renderedData.show_product_count && category.product_count !== undefined"
              class="text-xs text-gray-400 mt-0.5"
            >
              {{ category.product_count }} productos
            </p>
          </div>
        </a>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <p class="text-gray-400">No hay categorías disponibles</p>
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
  },
  methods: {
    handleImageError(event) {
      event.target.style.display = 'none';
    },
  },
};
</script>

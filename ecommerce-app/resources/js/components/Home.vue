<template>
  <div class="home-page">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center min-h-screen">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="container mx-auto px-4 py-8">
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
        <p class="font-bold">Error loading home page</p>
        <p class="text-sm">{{ error }}</p>
      </div>
    </div>

    <!-- Sections -->
    <div v-else>
      <component
        v-for="section in sections"
        :key="section.uuid"
        :is="getSectionComponent(section.type)"
        :section="section"
      />
    </div>
  </div>
</template>

<script>
import HeroSection from './sections/HeroSection.vue';
import FeaturedProductsSection from './sections/FeaturedProductsSection.vue';
import FeaturedCategoriesSection from './sections/FeaturedCategoriesSection.vue';
import BannersSection from './sections/BannersSection.vue';
import TestimonialsSection from './sections/TestimonialsSection.vue';
import HtmlBlockSection from './sections/HtmlBlockSection.vue';

export default {
  name: 'Home',
  components: {
    HeroSection,
    FeaturedProductsSection,
    FeaturedCategoriesSection,
    BannersSection,
    TestimonialsSection,
    HtmlBlockSection,
  },
  data() {
    return {
      sections: [],
      loading: true,
      error: null,
    };
  },
  mounted() {
    this.fetchConfiguration();
  },
  methods: {
    async fetchConfiguration() {
      try {
        this.loading = true;
        this.error = null;
        
        const response = await fetch('/api/home-configuration');
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        // Sort sections by display_order
        this.sections = (data.data || data).sort((a, b) => a.display_order - b.display_order);
      } catch (err) {
        this.error = err.message;
        console.error('Error fetching home configuration:', err);
      } finally {
        this.loading = false;
      }
    },
    getSectionComponent(type) {
      const componentMap = {
        hero: 'HeroSection',
        featured_products: 'FeaturedProductsSection',
        featured_categories: 'FeaturedCategoriesSection',
        banners: 'BannersSection',
        testimonials: 'TestimonialsSection',
        html_block: 'HtmlBlockSection',
      };
      
      return componentMap[type] || null;
    },
  },
};
</script>

<style scoped>
.home-page {
  min-height: 100vh;
}
</style>

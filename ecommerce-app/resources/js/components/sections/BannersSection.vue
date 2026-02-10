<template>
  <section class="banners-section py-12 md:py-16 lg:py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <!-- Section Title -->
      <div v-if="section.title" class="text-center mb-8 md:mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          {{ section.title }}
        </h2>
      </div>

      <!-- Slider Layout -->
      <div v-if="renderedData.layout === 'slider'" class="relative">
        <div 
          class="overflow-hidden rounded-lg"
          @touchstart="handleTouchStart"
          @touchend="handleTouchEnd"
        >
          <div
            class="flex transition-transform duration-500 ease-in-out"
            :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
          >
            <div
              v-for="(banner, index) in banners"
              :key="index"
              class="w-full flex-shrink-0"
            >
              <div class="relative aspect-[16/9] sm:aspect-[21/9] bg-gray-200 overflow-hidden">
                <img
                  v-if="banner.image"
                  :src="banner.image"
                  :alt="banner.title"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                  loading="lazy"
                />
                
                <!-- Banner Content Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent flex items-center">
                  <div class="container mx-auto px-4 sm:px-8 md:px-12">
                    <div class="max-w-2xl text-white">
                      <h3 v-if="banner.title" class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-2 sm:mb-3">
                        {{ banner.title }}
                      </h3>
                      <p v-if="banner.subtitle" class="text-base sm:text-lg md:text-xl mb-4 sm:mb-6">
                        {{ banner.subtitle }}
                      </p>
                      <a
                        v-if="banner.link && banner.button_text"
                        :href="banner.link"
                        class="inline-block bg-white text-gray-900 px-4 py-2 sm:px-6 sm:py-3 rounded-lg text-sm sm:text-base font-semibold hover:bg-gray-100 transition-colors"
                      >
                        {{ banner.button_text }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Arrows -->
        <button
          v-if="banners.length > 1"
          @click="previousSlide"
          class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-900 p-2 md:p-3 rounded-full shadow-lg transition-all z-10"
          aria-label="Previous slide"
        >
          <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button
          v-if="banners.length > 1"
          @click="nextSlide"
          class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-900 p-2 md:p-3 rounded-full shadow-lg transition-all z-10"
          aria-label="Next slide"
        >
          <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>

        <!-- Dots Indicator -->
        <div v-if="banners.length > 1" class="flex justify-center mt-6 gap-2">
          <button
            v-for="(banner, index) in banners"
            :key="index"
            @click="goToSlide(index)"
            :class="currentSlide === index ? 'bg-blue-600 w-8' : 'bg-gray-300 w-3'"
            class="h-3 rounded-full transition-all"
            :aria-label="`Go to slide ${index + 1}`"
          ></button>
        </div>
      </div>

      <!-- Grid Layout -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div
          v-for="(banner, index) in banners"
          :key="index"
          class="relative aspect-video bg-gray-200 rounded-lg overflow-hidden group"
        >
          <img
            v-if="banner.image"
            :src="banner.image"
            :alt="banner.title"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            @error="handleImageError"
            loading="lazy"
          />
          
          <!-- Banner Content Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
            <div class="p-4 sm:p-6 text-white w-full">
              <h3 v-if="banner.title" class="text-lg sm:text-xl md:text-2xl font-bold mb-1 sm:mb-2">
                {{ banner.title }}
              </h3>
              <p v-if="banner.subtitle" class="text-xs sm:text-sm mb-2 sm:mb-3">
                {{ banner.subtitle }}
              </p>
              <a
                v-if="banner.link && banner.button_text"
                :href="banner.link"
                class="inline-block bg-white text-gray-900 px-3 py-1.5 sm:px-4 sm:py-2 rounded text-xs sm:text-sm font-semibold hover:bg-gray-100 transition-colors"
              >
                {{ banner.button_text }}
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!banners.length" class="text-center py-12">
        <p class="text-gray-500">No banners available</p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'BannersSection',
  props: {
    section: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      currentSlide: 0,
      autoplayInterval: null,
      touchStartX: 0,
      touchEndX: 0,
    };
  },
  computed: {
    renderedData() {
      return this.section.rendered_data || {};
    },
    banners() {
      return this.renderedData.banners || [];
    },
  },
  mounted() {
    if (this.renderedData.layout === 'slider' && this.renderedData.autoplay && this.banners.length > 1) {
      this.startAutoplay();
    }
  },
  beforeUnmount() {
    this.stopAutoplay();
  },
  methods: {
    nextSlide() {
      this.currentSlide = (this.currentSlide + 1) % this.banners.length;
    },
    previousSlide() {
      this.currentSlide = this.currentSlide === 0 ? this.banners.length - 1 : this.currentSlide - 1;
    },
    goToSlide(index) {
      this.currentSlide = index;
    },
    startAutoplay() {
      const speed = this.renderedData.autoplay_speed || 5000;
      this.autoplayInterval = setInterval(() => {
        this.nextSlide();
      }, speed);
    },
    stopAutoplay() {
      if (this.autoplayInterval) {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
      }
    },
    handleTouchStart(e) {
      this.touchStartX = e.changedTouches[0].screenX;
    },
    handleTouchEnd(e) {
      this.touchEndX = e.changedTouches[0].screenX;
      this.handleSwipe();
    },
    handleSwipe() {
      const swipeThreshold = 50;
      const diff = this.touchStartX - this.touchEndX;
      
      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          // Swipe left - next slide
          this.nextSlide();
        } else {
          // Swipe right - previous slide
          this.previousSlide();
        }
      }
    },
    handleImageError(event) {
      console.warn('Banner image failed to load:', event.target.src);
      // Keep the gray background as fallback
      event.target.style.display = 'none';
    },
  },
};
</script>

<style scoped>
/* Additional styles if needed */
</style>

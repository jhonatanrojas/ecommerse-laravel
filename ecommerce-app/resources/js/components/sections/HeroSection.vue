<template>
  <section class="hero-section relative overflow-hidden">
    <!-- Background Image -->
    <div
      v-if="renderedData.background_image"
      class="absolute inset-0 bg-cover bg-center"
      :style="{ backgroundImage: `url(${renderedData.background_image})` }"
      @error="handleBackgroundError"
    ></div>

    <!-- Background Video -->
    <video
      v-if="renderedData.background_video"
      class="absolute inset-0 w-full h-full object-cover"
      autoplay
      loop
      muted
      playsinline
      @error="handleVideoError"
    >
      <source :src="renderedData.background_video" type="video/mp4" />
    </video>

    <!-- Overlay -->
    <div
      class="absolute inset-0 bg-black"
      :style="{ opacity: renderedData.overlay_opacity || 0.5 }"
    ></div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-4 py-16 sm:py-24 md:py-32 lg:py-40">
      <div class="max-w-4xl mx-auto text-center text-white">
        <!-- Title -->
        <h1
          v-if="renderedData.title"
          class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-3 sm:mb-4 md:mb-6 leading-tight"
        >
          {{ renderedData.title }}
        </h1>

        <!-- Subtitle -->
        <p
          v-if="renderedData.subtitle"
          class="text-base sm:text-lg md:text-xl lg:text-2xl mb-6 sm:mb-8 md:mb-10 text-gray-200"
        >
          {{ renderedData.subtitle }}
        </p>

        <!-- CTA Buttons -->
        <div
          v-if="renderedData.cta_buttons && renderedData.cta_buttons.length"
          class="flex flex-wrap justify-center gap-3 sm:gap-4"
        >
          <a
            v-for="(button, index) in renderedData.cta_buttons"
            :key="index"
            :href="button.url"
            :class="getButtonClass(button.style)"
            class="px-5 py-2.5 sm:px-6 sm:py-3 md:px-8 md:py-4 rounded-lg font-semibold text-sm sm:text-base md:text-lg transition-all duration-300 hover:scale-105 active:scale-95"
          >
            {{ button.text }}
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroSection',
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
  },
  methods: {
    getButtonClass(style) {
      const styles = {
        primary: 'bg-blue-600 hover:bg-blue-700 text-white',
        secondary: 'bg-white hover:bg-gray-100 text-gray-900',
        outline: 'border-2 border-white hover:bg-white hover:text-gray-900 text-white',
      };
      return styles[style] || styles.primary;
    },
    handleBackgroundError(event) {
      console.warn('Hero background image failed to load:', this.renderedData.background_image);
      // Optionally set a fallback background color
      event.target.style.backgroundColor = '#4F46E5';
    },
    handleVideoError(event) {
      console.warn('Hero background video failed to load:', this.renderedData.background_video);
      // Hide the video element
      event.target.style.display = 'none';
    },
  },
};
</script>

<style scoped>
.hero-section {
  min-height: 400px;
}

@media (min-width: 640px) {
  .hero-section {
    min-height: 500px;
  }
}

@media (min-width: 768px) {
  .hero-section {
    min-height: 600px;
  }
}

@media (min-width: 1024px) {
  .hero-section {
    min-height: 700px;
  }
}
</style>

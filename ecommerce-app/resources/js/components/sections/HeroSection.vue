<template>
  <section class="hero-section relative overflow-hidden">
    <!-- Background Image -->
    <div
      v-if="renderedData.background_image"
      class="absolute inset-0 bg-cover bg-center"
      :style="{ backgroundImage: `url(${renderedData.background_image})` }"
    ></div>

    <!-- Background Video -->
    <video
      v-if="renderedData.background_video"
      class="absolute inset-0 w-full h-full object-cover"
      autoplay
      loop
      muted
      playsinline
    >
      <source :src="renderedData.background_video" type="video/mp4" />
    </video>

    <!-- Gradient Overlay -->
    <div
      class="absolute inset-0"
      :style="{
        background: `linear-gradient(135deg, rgba(0,0,0,${renderedData.overlay_opacity || 0.6}) 0%, rgba(0,0,0,${(renderedData.overlay_opacity || 0.6) * 0.4}) 100%)`
      }"
    ></div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-4 py-20 sm:py-28 md:py-36 lg:py-44">
      <div class="max-w-3xl">
        <!-- Eyebrow -->
        <div
          v-if="renderedData.eyebrow"
          class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm text-white text-xs sm:text-sm font-medium px-4 py-2 rounded-full mb-6 animate-fade-in"
        >
          <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
          {{ renderedData.eyebrow }}
        </div>

        <!-- Title -->
        <h1
          v-if="renderedData.title"
          class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-white mb-5 leading-[1.1] animate-fade-in-up drop-shadow-md"
          style="letter-spacing: -0.03em; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"
        >
          {{ renderedData.title }}
        </h1>

        <!-- Subtitle -->
        <p
          v-if="renderedData.subtitle"
          class="text-lg sm:text-xl md:text-2xl text-gray-200 mb-8 max-w-xl leading-relaxed animate-fade-in-up drop-shadow-sm"
          style="animation-delay: 150ms; text-shadow: 0 1px 2px rgba(0,0,0,0.5);"
        >
          {{ renderedData.subtitle }}
        </p>

        <!-- CTA Buttons -->
        <div
          v-if="renderedData.cta_buttons && renderedData.cta_buttons.length"
          class="flex flex-wrap gap-4 animate-fade-in-up"
          style="animation-delay: 300ms;"
        >
          <a
            v-for="(button, index) in renderedData.cta_buttons"
            :key="index"
            :href="button.url"
            :class="getButtonClass(button.style)"
            class="text-sm sm:text-base"
          >
            {{ button.text }}
            <svg v-if="index === 0" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </a>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 animate-scroll-bounce z-10 hidden md:block">
      <div class="w-8 h-12 border-2 border-white/40 rounded-full flex justify-center pt-2">
        <div class="w-1.5 h-3 bg-white/60 rounded-full animate-pulse"></div>
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
        primary: 'btn-primary animate-pulse-glow',
        secondary: 'btn-outline',
        outline: 'btn-outline',
      };
      return styles[style] || styles.primary;
    },
  },
};
</script>

<style scoped>
.hero-section {
  min-height: 85vh;
}

@media (max-width: 640px) {
  .hero-section {
    min-height: 70vh;
  }
}
</style>

<template>
  <section class="section-padding bg-gray-50">
    <div class="container mx-auto px-4">
      <!-- Section Header -->
      <div class="section-header">
        <h2 class="section-title">{{ section.title }}</h2>
        <p v-if="section.subtitle" class="section-subtitle">{{ section.subtitle }}</p>
      </div>

      <!-- Carousel Layout -->
      <div v-if="renderedData.layout === 'carousel'" class="relative max-w-3xl mx-auto">
        <div
          class="overflow-hidden"
          @touchstart="handleTouchStart"
          @touchend="handleTouchEnd"
        >
          <div
            class="flex transition-transform duration-500 ease-in-out"
            :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
          >
            <div
              v-for="(testimonial, index) in testimonials"
              :key="index"
              class="w-full flex-shrink-0 px-2"
            >
              <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm border-l-4 border-indigo-500">
                <!-- Stars -->
                <div v-if="renderedData.show_rating && testimonial.rating" class="flex gap-0.5 mb-4">
                  <svg v-for="star in 5" :key="star" class="w-5 h-5" :class="star <= testimonial.rating ? 'text-amber-400 fill-current' : 'text-gray-200 fill-current'" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>

                <!-- Quote -->
                <p class="text-base md:text-lg text-gray-700 mb-6 leading-relaxed italic">
                  "{{ testimonial.text }}"
                </p>

                <!-- Author -->
                <div class="flex items-center">
                  <div v-if="renderedData.show_avatar" class="flex-shrink-0 mr-3">
                    <img
                      v-if="testimonial.avatar"
                      :src="testimonial.avatar"
                      :alt="testimonial.name"
                      class="w-11 h-11 rounded-full object-cover ring-2 ring-indigo-100"
                      loading="lazy"
                    />
                    <div v-else class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                      {{ getInitials(testimonial.name) }}
                    </div>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ testimonial.name }}</p>
                    <p v-if="testimonial.date" class="text-xs text-gray-400 mt-0.5">{{ formatDate(testimonial.date) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Arrows -->
        <button
          v-if="testimonials.length > 1"
          @click="previousSlide"
          class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-2 md:-translate-x-5 bg-white hover:bg-gray-50 text-gray-700 p-2.5 rounded-full shadow-lg transition-all z-10"
          aria-label="Testimonio anterior"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button
          v-if="testimonials.length > 1"
          @click="nextSlide"
          class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-2 md:translate-x-5 bg-white hover:bg-gray-50 text-gray-700 p-2.5 rounded-full shadow-lg transition-all z-10"
          aria-label="Siguiente testimonio"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        <!-- Dots -->
        <div v-if="testimonials.length > 1" class="flex justify-center mt-8 gap-2">
          <button
            v-for="(t, index) in testimonials"
            :key="index"
            @click="goToSlide(index)"
            :class="currentSlide === index ? 'bg-indigo-600 w-8' : 'bg-gray-300 w-3'"
            class="h-3 rounded-full transition-all duration-300"
            :aria-label="`Ir al testimonio ${index + 1}`"
          ></button>
        </div>
      </div>

      <!-- Grid Layout -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(testimonial, index) in testimonials"
          :key="index"
          class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-indigo-500 animate-fade-in-up"
          :style="{ animationDelay: `${index * 100}ms` }"
        >
          <div v-if="renderedData.show_rating && testimonial.rating" class="flex gap-0.5 mb-3">
            <svg v-for="star in 5" :key="star" class="w-4 h-4" :class="star <= testimonial.rating ? 'text-amber-400 fill-current' : 'text-gray-200 fill-current'" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
          </div>
          <p class="text-sm text-gray-700 mb-4 italic leading-relaxed">"{{ testimonial.text }}"</p>
          <div class="flex items-center">
            <div v-if="renderedData.show_avatar" class="flex-shrink-0 mr-3">
              <img v-if="testimonial.avatar" :src="testimonial.avatar" :alt="testimonial.name" class="w-9 h-9 rounded-full object-cover" loading="lazy" />
              <div v-else class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xs">
                {{ getInitials(testimonial.name) }}
              </div>
            </div>
            <p class="font-semibold text-gray-900 text-sm">{{ testimonial.name }}</p>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!testimonials.length" class="text-center py-12">
        <p class="text-gray-400">No hay testimonios disponibles</p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'TestimonialsSection',
  props: {
    section: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      currentSlide: 0,
      touchStartX: 0,
      touchEndX: 0,
    };
  },
  computed: {
    renderedData() {
      return this.section.rendered_data || {};
    },
    testimonials() {
      return this.renderedData.testimonials || [];
    },
  },
  methods: {
    nextSlide() {
      this.currentSlide = (this.currentSlide + 1) % this.testimonials.length;
    },
    previousSlide() {
      this.currentSlide = this.currentSlide === 0 ? this.testimonials.length - 1 : this.currentSlide - 1;
    },
    goToSlide(index) {
      this.currentSlide = index;
    },
    getInitials(name) {
      if (!name) return '?';
      const parts = name.split(' ');
      return parts.length >= 2 ? (parts[0][0] + parts[1][0]).toUpperCase() : name.substring(0, 2).toUpperCase();
    },
    formatDate(dateString) {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
    },
    handleTouchStart(e) { this.touchStartX = e.changedTouches[0].screenX; },
    handleTouchEnd(e) {
      this.touchEndX = e.changedTouches[0].screenX;
      const diff = this.touchStartX - this.touchEndX;
      if (Math.abs(diff) > 50) { diff > 0 ? this.nextSlide() : this.previousSlide(); }
    },
  },
};
</script>

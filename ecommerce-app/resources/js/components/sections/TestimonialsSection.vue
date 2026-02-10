<template>
  <section class="testimonials-section py-12 md:py-16 lg:py-20 bg-white">
    <div class="container mx-auto px-4">
      <!-- Section Title -->
      <div class="text-center mb-8 md:mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          {{ section.title }}
        </h2>
      </div>

      <!-- Carousel Layout -->
      <div v-if="renderedData.layout === 'carousel'" class="relative max-w-4xl mx-auto">
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
              class="w-full flex-shrink-0 px-2 sm:px-4"
            >
              <div class="bg-gray-50 rounded-lg p-6 sm:p-8 md:p-12">
                <!-- Quote Icon -->
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600 mb-3 sm:mb-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                </svg>

                <!-- Testimonial Text -->
                <p class="text-base sm:text-lg md:text-xl text-gray-700 mb-4 sm:mb-6 italic">
                  "{{ testimonial.text }}"
                </p>

                <!-- Author Info -->
                <div class="flex items-center">
                  <!-- Avatar -->
                  <div
                    v-if="renderedData.show_avatar"
                    class="flex-shrink-0 mr-3 sm:mr-4"
                  >
                    <img
                      v-if="testimonial.avatar"
                      :src="testimonial.avatar"
                      :alt="testimonial.name"
                      class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover"
                      @error="handleAvatarError"
                      loading="lazy"
                    />
                    <div
                      v-else
                      class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm"
                    >
                      {{ getInitials(testimonial.name) }}
                    </div>
                  </div>

                  <div class="flex-1">
                    <!-- Name -->
                    <p class="font-semibold text-gray-900 text-sm sm:text-base">
                      {{ testimonial.name }}
                    </p>

                    <!-- Rating -->
                    <div
                      v-if="renderedData.show_rating && testimonial.rating"
                      class="flex items-center mt-1"
                    >
                      <div class="flex text-yellow-400">
                        <svg
                          v-for="star in 5"
                          :key="star"
                          class="w-4 h-4"
                          :class="star <= testimonial.rating ? 'fill-current' : 'fill-gray-300'"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                    </div>

                    <!-- Date -->
                    <p v-if="testimonial.date" class="text-sm text-gray-500 mt-1">
                      {{ formatDate(testimonial.date) }}
                    </p>
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
          class="absolute left-0 top-1/2 -translate-y-1/2 md:-translate-x-4 bg-white hover:bg-gray-50 text-gray-900 p-2 md:p-3 rounded-full shadow-lg transition-all z-10"
          aria-label="Previous testimonial"
        >
          <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button
          v-if="testimonials.length > 1"
          @click="nextSlide"
          class="absolute right-0 top-1/2 -translate-y-1/2 md:translate-x-4 bg-white hover:bg-gray-50 text-gray-900 p-2 md:p-3 rounded-full shadow-lg transition-all z-10"
          aria-label="Next testimonial"
        >
          <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>

        <!-- Dots Indicator -->
        <div v-if="testimonials.length > 1" class="flex justify-center mt-8 gap-2">
          <button
            v-for="(testimonial, index) in testimonials"
            :key="index"
            @click="goToSlide(index)"
            :class="currentSlide === index ? 'bg-blue-600 w-8' : 'bg-gray-300 w-3'"
            class="h-3 rounded-full transition-all"
            :aria-label="`Go to testimonial ${index + 1}`"
          ></button>
        </div>
      </div>

      <!-- Grid Layout -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div
          v-for="(testimonial, index) in testimonials"
          :key="index"
          class="bg-gray-50 rounded-lg p-4 sm:p-6"
        >
          <!-- Quote Icon -->
          <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600 mb-2 sm:mb-3" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
          </svg>

          <!-- Testimonial Text -->
          <p class="text-sm sm:text-base text-gray-700 mb-3 sm:mb-4 italic">
            "{{ testimonial.text }}"
          </p>

          <!-- Author Info -->
          <div class="flex items-center">
            <!-- Avatar -->
            <div
              v-if="renderedData.show_avatar"
              class="flex-shrink-0 mr-3"
            >
              <img
                v-if="testimonial.avatar"
                :src="testimonial.avatar"
                :alt="testimonial.name"
                class="w-10 h-10 rounded-full object-cover"
                @error="handleAvatarError"
                loading="lazy"
              />
              <div
                v-else
                class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm"
              >
                {{ getInitials(testimonial.name) }}
              </div>
            </div>

            <div class="flex-1">
              <!-- Name -->
              <p class="font-semibold text-gray-900 text-sm">
                {{ testimonial.name }}
              </p>

              <!-- Rating -->
              <div
                v-if="renderedData.show_rating && testimonial.rating"
                class="flex items-center mt-1"
              >
                <div class="flex text-yellow-400">
                  <svg
                    v-for="star in 5"
                    :key="star"
                    class="w-3 h-3"
                    :class="star <= testimonial.rating ? 'fill-current' : 'fill-gray-300'"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!testimonials.length" class="text-center py-12">
        <p class="text-gray-500">No testimonials available</p>
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
      if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
      }
      return name.substring(0, 2).toUpperCase();
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
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
    handleAvatarError(event) {
      console.warn('Avatar image failed to load:', event.target.src);
      // Replace with initials fallback
      const img = event.target;
      const name = img.alt;
      const initials = this.getInitials(name);
      
      // Create a fallback div
      const fallback = document.createElement('div');
      fallback.className = 'w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm';
      fallback.textContent = initials;
      
      // Replace the image with the fallback
      img.parentNode.replaceChild(fallback, img);
    },
  },
};
</script>

<style scoped>
/* Additional styles if needed */
</style>

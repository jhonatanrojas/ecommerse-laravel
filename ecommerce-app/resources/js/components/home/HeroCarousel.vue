<template>
  <section
    ref="carouselRef"
    class="relative overflow-hidden rounded-3xl"
    role="region"
    aria-label="Ofertas destacadas"
    tabindex="0"
    @mouseenter="pauseAutoPlay"
    @mouseleave="resumeAutoPlay"
    @focusin="onFocusIn"
    @focusout="onFocusOut"
    @keydown="onKeydown"
    @touchstart.passive="onTouchStart"
    @touchend.passive="onTouchEnd"
  >
    <Transition :name="transitionName" mode="out-in">
      <article
        :key="currentSlide.id"
        class="relative h-[500px] md:h-[600px]"
        :class="currentSlide.gradient || defaultGradient"
      >
        <img
          v-if="currentSlide.image"
          class="absolute inset-0 h-full w-full object-cover opacity-35 md:opacity-90 lazyload"
          :data-src="currentSlide.image"
          :alt="currentSlide.title"
        >
        <div class="absolute inset-0 bg-black/35 md:bg-transparent"></div>

        <div class="relative z-10 grid h-full grid-cols-1 md:grid-cols-5">
          <div class="flex flex-col justify-center px-6 py-10 text-white md:col-span-3 md:px-10 lg:px-14">
            <p v-if="currentSlide.badge" class="mb-4 inline-flex w-fit rounded-full bg-white/20 px-3 py-1 text-xs font-bold tracking-wide backdrop-blur-sm">
              {{ currentSlide.badge }}
            </p>
            <h2 class="text-3xl font-black leading-tight md:text-5xl">{{ currentSlide.title }}</h2>
            <p class="mt-3 max-w-xl text-sm text-white/90 md:text-lg">{{ currentSlide.subtitle }}</p>

            <CountdownTimer
              v-if="showCountdown"
              class="mt-5"
              :end-time="currentSlide.endTime"
              @expired="onOfferExpired"
            />

            <a
              :href="currentSlide.cta?.link || '#'
              "
              class="mt-6 inline-flex w-fit items-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-gray-900 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-black/20"
            >
              {{ currentSlide.cta?.text || 'Ver oferta' }}
            </a>
          </div>

          <div class="hidden md:col-span-2 md:flex md:items-end md:justify-end md:pr-8">
            <img
              v-if="currentSlide.image"
              class="max-h-[80%] w-auto max-w-[92%] object-contain drop-shadow-2xl lazyload"
              :data-src="currentSlide.image"
              :alt="currentSlide.title"
            >
          </div>
        </div>
      </article>
    </Transition>

    <button
      type="button"
      class="absolute left-2 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/90 p-2 text-gray-900 shadow transition hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
      aria-label="Slide anterior"
      @click="prevSlide"
    >
      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18 9 12l6-6"/>
      </svg>
    </button>

    <button
      type="button"
      class="absolute right-2 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/90 p-2 text-gray-900 shadow transition hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
      aria-label="Slide siguiente"
      @click="nextSlide"
    >
      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
      </svg>
    </button>

    <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2 rounded-full bg-black/25 px-3 py-2 backdrop-blur-sm">
      <button
        v-for="(slide, index) in normalizedSlides"
        :key="slide.id"
        type="button"
        :aria-label="`Ir al slide ${index + 1}`"
        class="h-2.5 w-2.5 rounded-full transition"
        :class="index === activeIndex ? 'bg-white' : 'bg-white/45 hover:bg-white/70'"
        @click="goTo(index)"
      />
    </div>
  </section>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import CountdownTimer from './CountdownTimer.vue';

const props = defineProps({
  slides: {
    type: Array,
    default: () => [],
  },
  autoplayMs: {
    type: Number,
    default: 5000,
  },
});

const emit = defineEmits(['slide-change', 'offer-expired']);

const defaultGradient = 'bg-gradient-to-br from-blue-700 via-indigo-700 to-slate-900';
const activeIndex = ref(0);
const autoplayTimer = ref(null);
const isPaused = ref(false);
const hasFocusInside = ref(false);
const touchStartX = ref(0);
const transitionName = ref('slide-next');
const carouselRef = ref(null);

const normalizedSlides = computed(() => {
  const raw = Array.isArray(props.slides) ? props.slides.filter(Boolean) : [];
  return raw.slice(0, 5);
});

const currentSlide = computed(() => normalizedSlides.value[activeIndex.value] || {});
const showCountdown = computed(() => currentSlide.value.type === 'offer' && Boolean(currentSlide.value.endTime));

function restartAutoplay() {
  clearInterval(autoplayTimer.value);
  if (normalizedSlides.value.length <= 1 || isPaused.value || hasFocusInside.value) return;

  autoplayTimer.value = setInterval(() => {
    nextSlide();
  }, props.autoplayMs);
}

function pauseAutoPlay() {
  isPaused.value = true;
  restartAutoplay();
}

function resumeAutoPlay() {
  isPaused.value = false;
  restartAutoplay();
}

function goTo(index) {
  if (!normalizedSlides.value.length) return;
  transitionName.value = index > activeIndex.value ? 'slide-next' : 'slide-prev';
  activeIndex.value = index;
  emit('slide-change', normalizedSlides.value[index]);
  restartAutoplay();
}

function nextSlide() {
  if (!normalizedSlides.value.length) return;
  transitionName.value = 'slide-next';
  activeIndex.value = (activeIndex.value + 1) % normalizedSlides.value.length;
  emit('slide-change', currentSlide.value);
}

function prevSlide() {
  if (!normalizedSlides.value.length) return;
  transitionName.value = 'slide-prev';
  activeIndex.value = (activeIndex.value - 1 + normalizedSlides.value.length) % normalizedSlides.value.length;
  emit('slide-change', currentSlide.value);
  restartAutoplay();
}

function onOfferExpired() {
  emit('offer-expired', currentSlide.value);
}

function onKeydown(event) {
  if (event.key === 'ArrowRight') {
    event.preventDefault();
    nextSlide();
    restartAutoplay();
  }

  if (event.key === 'ArrowLeft') {
    event.preventDefault();
    prevSlide();
  }

  if (event.key === 'Tab' && hasFocusInside.value) {
    const focusable = carouselRef.value?.querySelectorAll('button, a, [tabindex]:not([tabindex="-1"])');
    if (!focusable?.length) return;

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    } else if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    }
  }
}

function onFocusIn() {
  hasFocusInside.value = true;
  restartAutoplay();
}

function onFocusOut() {
  requestAnimationFrame(() => {
    const stillInside = carouselRef.value?.contains(document.activeElement);
    hasFocusInside.value = Boolean(stillInside);
    restartAutoplay();
  });
}

function onTouchStart(event) {
  touchStartX.value = event.changedTouches?.[0]?.clientX || 0;
}

function onTouchEnd(event) {
  const endX = event.changedTouches?.[0]?.clientX || 0;
  const delta = endX - touchStartX.value;

  if (Math.abs(delta) < 40) return;
  if (delta > 0) {
    prevSlide();
  } else {
    nextSlide();
  }
  restartAutoplay();
}

onMounted(() => {
  restartAutoplay();
});

onBeforeUnmount(() => {
  clearInterval(autoplayTimer.value);
});
</script>

<style scoped>
.slide-next-enter-active,
.slide-next-leave-active,
.slide-prev-enter-active,
.slide-prev-leave-active {
  transition: all 0.45s ease;
}

.slide-next-enter-from,
.slide-prev-leave-to {
  opacity: 0;
  transform: translateX(40px);
}

.slide-next-leave-to,
.slide-prev-enter-from {
  opacity: 0;
  transform: translateX(-40px);
}
</style>

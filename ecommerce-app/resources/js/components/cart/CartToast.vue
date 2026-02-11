<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="visible"
        class="fixed bottom-6 right-6 z-50 max-w-sm"
      >
        <div
          :class="toastClass"
          class="flex items-start gap-3 p-4 rounded-lg shadow-lg border"
        >
          <!-- Icon -->
          <div class="flex-shrink-0">
            <svg v-if="type === 'success'" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg v-else-if="type === 'error'" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg v-else class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <p v-if="title" class="text-sm font-semibold mb-1" :class="titleClass">
              {{ title }}
            </p>
            <p class="text-sm" :class="messageClass">
              {{ message }}
            </p>
          </div>

          <!-- Close Button -->
          <button
            @click="close"
            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"
            aria-label="Cerrar"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'info'].includes(value),
  },
  title: {
    type: String,
    default: '',
  },
  message: {
    type: String,
    required: true,
  },
  duration: {
    type: Number,
    default: 3000,
  },
  show: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close']);

const visible = ref(props.show);
let timeoutId = null;

const toastClass = computed(() => {
  const classes = {
    success: 'bg-green-50 border-green-200',
    error: 'bg-red-50 border-red-200',
    info: 'bg-blue-50 border-blue-200',
  };
  return classes[props.type] || classes.info;
});

const titleClass = computed(() => {
  const classes = {
    success: 'text-green-900',
    error: 'text-red-900',
    info: 'text-blue-900',
  };
  return classes[props.type] || classes.info;
});

const messageClass = computed(() => {
  const classes = {
    success: 'text-green-700',
    error: 'text-red-700',
    info: 'text-blue-700',
  };
  return classes[props.type] || classes.info;
});

watch(() => props.show, (newValue) => {
  visible.value = newValue;
  
  if (newValue && props.duration > 0) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      close();
    }, props.duration);
  }
});

const close = () => {
  visible.value = false;
  clearTimeout(timeoutId);
  emit('close');
};
</script>

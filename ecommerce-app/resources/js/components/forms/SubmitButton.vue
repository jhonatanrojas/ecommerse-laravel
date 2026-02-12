<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    class="btn-primary w-full px-6 py-3 text-sm font-semibold"
    :class="buttonClasses"
    @click="$emit('click', $event)"
  >
    <svg
      v-if="loading"
      class="mr-2 h-4 w-4 animate-spin"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z" />
    </svg>

    <span>{{ loading ? loadingText : text }}</span>
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  text: {
    type: String,
    required: true,
  },
  loadingText: {
    type: String,
    default: 'Procesando...',
  },
  type: {
    type: String,
    default: 'submit',
  },
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success'].includes(value),
  },
});

defineEmits(['click']);

const buttonClasses = computed(() => {
  if (props.disabled || props.loading) {
    return 'cursor-not-allowed bg-gray-300 hover:bg-gray-300 shadow-none';
  }

  if (props.variant === 'secondary') {
    return 'bg-gray-700 hover:bg-gray-800';
  }

  if (props.variant === 'success') {
    return 'bg-emerald-600 hover:bg-emerald-700';
  }

  return '';
});
</script>

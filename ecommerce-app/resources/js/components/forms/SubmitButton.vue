<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
    class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white transition-all duration-200"
    :class="[
      disabled || loading
        ? 'bg-gray-400 cursor-not-allowed'
        : variant === 'primary'
        ? 'bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300'
        : variant === 'secondary'
        ? 'bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:ring-gray-300'
        : 'bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300'
    ]"
  >
    <svg 
      v-if="loading" 
      class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" 
      xmlns="http://www.w3.org/2000/svg" 
      fill="none" 
      viewBox="0 0 24 24"
    >
      <circle 
        class="opacity-25" 
        cx="12" 
        cy="12" 
        r="10" 
        stroke="currentColor" 
        stroke-width="4"
      />
      <path 
        class="opacity-75" 
        fill="currentColor" 
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      />
    </svg>
    
    <span>{{ loading ? loadingText : text }}</span>
  </button>
</template>

<script setup>
defineProps({
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
</script>

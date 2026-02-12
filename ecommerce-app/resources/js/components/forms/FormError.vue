<template>
  <div
    v-if="message"
    class="rounded-xl border p-4"
    :class="type === 'error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-indigo-200 bg-indigo-50 text-indigo-800'"
  >
    <div class="flex items-start gap-3">
      <div class="mt-0.5 shrink-0">
        <svg v-if="type === 'error'" class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <svg v-else class="h-5 w-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
      </div>

      <div class="min-w-0 flex-1">
        <p class="text-sm font-medium">{{ message }}</p>
        <ul v-if="errors && Object.keys(errors).length > 0" class="mt-2 list-inside list-disc space-y-1 text-sm">
          <li v-for="(errorMessages, field) in errors" :key="field">
            {{ errorMessages[0] }}
          </li>
        </ul>
      </div>

      <button
        v-if="dismissible"
        type="button"
        class="shrink-0 rounded-md p-1.5"
        :class="type === 'error' ? 'text-red-500 hover:bg-red-100' : 'text-indigo-500 hover:bg-indigo-100'"
        @click="$emit('dismiss')"
      >
        <span class="sr-only">Cerrar</span>
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: '',
  },
  errors: {
    type: Object,
    default: null,
  },
  type: {
    type: String,
    default: 'error',
    validator: (value) => ['error', 'info'].includes(value),
  },
  dismissible: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['dismiss']);
</script>

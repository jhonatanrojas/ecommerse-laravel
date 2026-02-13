<template>
  <form class="space-y-2" @submit.prevent="submit">
    <textarea
      v-model="text"
      rows="3"
      placeholder="Escribe tu pregunta para el vendedor"
      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[var(--mp-accent)] focus:outline-none focus:ring-2 focus:ring-[var(--mp-accent-soft)]"
    />
    <div class="flex items-center justify-between">
      <p class="text-xs text-gray-500">Las preguntas se publican en este producto.</p>
      <button class="mp-btn-primary" :disabled="loading || !text.trim()">
        {{ loading ? 'Enviando...' : 'Preguntar' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['submit']);
const text = ref('');

function submit() {
  const value = text.value.trim();
  if (!value) return;
  emit('submit', value);
  text.value = '';
}
</script>

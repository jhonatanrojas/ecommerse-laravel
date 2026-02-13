<template>
  <section class="mp-card p-4">
    <h3 class="text-lg font-black text-gray-900">Preguntas y respuestas</h3>

    <MarketplaceQuestionForm :loading="loading" @submit="$emit('submit-question', $event)" />

    <div class="mt-4 space-y-4">
      <article v-for="item in questions" :key="item.uuid" class="rounded-xl border border-gray-100 bg-gray-50 p-3">
        <p class="text-sm font-semibold text-gray-800">{{ item.question }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ item.asked_by || 'Comprador' }} - {{ formatDate(item.asked_at) }}</p>
        <div v-if="item.answer" class="mt-3 rounded border-l-4 border-[var(--mp-primary)] bg-white p-3">
          <p class="text-sm text-gray-700">{{ item.answer }}</p>
          <p class="mt-1 text-xs text-gray-500">Respondió {{ item.answered_by || 'Vendedor' }}</p>
        </div>
      </article>
      <p v-if="!questions.length" class="text-sm text-gray-500">Aún no hay preguntas para este producto.</p>
    </div>
  </section>
</template>

<script setup>
import MarketplaceQuestionForm from './MarketplaceQuestionForm.vue';

defineProps({
  questions: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['submit-question']);

function formatDate(value) {
  if (!value) return '';
  return new Date(value).toLocaleString('es-VE');
}
</script>

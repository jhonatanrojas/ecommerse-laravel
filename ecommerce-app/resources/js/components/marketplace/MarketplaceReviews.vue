<template>
  <section class="mp-card p-4">
    <div class="mb-4 flex items-end justify-between gap-3">
      <div>
        <h3 class="text-lg font-black text-gray-900">Opiniones del producto</h3>
        <p class="text-sm text-gray-600">{{ summary.reviews_count || 0 }} reseñas · {{ Number(summary.rating || 0).toFixed(1) }} / 5</p>
      </div>
      <button class="mp-btn-secondary" type="button" @click="showForm = !showForm">
        {{ showForm ? 'Cancelar' : 'Escribir opinion' }}
      </button>
    </div>

    <form v-if="showForm" class="mb-5 space-y-2 rounded-xl border border-gray-200 bg-gray-50 p-3" @submit.prevent="submit">
      <div class="flex items-center gap-2 text-sm">
        <label for="rating">Calificacion:</label>
        <select id="rating" v-model.number="form.rating" class="rounded-lg border border-gray-300 px-2 py-1">
          <option :value="5">5</option>
          <option :value="4">4</option>
          <option :value="3">3</option>
          <option :value="2">2</option>
          <option :value="1">1</option>
        </select>
      </div>
      <input v-model="form.title" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Titulo de tu opinion">
      <textarea v-model="form.comment" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Comparte tu experiencia"></textarea>
      <button class="mp-btn-primary" type="submit" :disabled="loading">{{ loading ? 'Guardando...' : 'Publicar opinion' }}</button>
    </form>

    <div class="space-y-3">
      <article v-for="review in reviews" :key="review.uuid" class="rounded-xl border border-gray-200 bg-white p-3">
        <p class="text-sm font-semibold text-gray-800">{{ review.title || 'Reseña' }}</p>
        <p class="mt-1 text-xs text-amber-500">{{ stars(review.rating) }}</p>
        <p class="mt-2 text-sm text-gray-700">{{ review.comment || 'Sin comentario.' }}</p>
        <p class="mt-2 text-xs text-gray-500">{{ review.author?.name || 'Cliente' }} · {{ formatDate(review.created_at) }}</p>
      </article>
      <p v-if="!reviews.length" class="text-sm text-gray-500">Este producto aun no tiene opiniones.</p>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref } from 'vue';

const props = defineProps({
  reviews: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({ rating: 0, reviews_count: 0 }) },
  loading: { type: Boolean, default: false },
});

const emit = defineEmits(['submit-review']);
const showForm = ref(false);
const form = reactive({ rating: 5, title: '', comment: '' });

function submit() {
  emit('submit-review', { ...form });
  form.rating = 5;
  form.title = '';
  form.comment = '';
  showForm.value = false;
}

function stars(value) {
  const rating = Math.max(1, Math.min(5, Number(value || 0)));
  return `${'★'.repeat(Math.round(rating))}${'☆'.repeat(5 - Math.round(rating))}`;
}

function formatDate(value) {
  if (!value) return '';
  return new Date(value).toLocaleDateString('es-VE');
}
</script>

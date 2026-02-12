<template>
  <nav v-if="lastPage > 1" class="mt-8 flex flex-wrap items-center justify-center gap-2" aria-label="Paginación">
    <button
      class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
      :disabled="!hasPrev || loading"
      @click="$emit('page-change', currentPage - 1)"
    >
      Anterior
    </button>

    <button
      v-for="page in visiblePages"
      :key="`page-${page}`"
      class="min-w-[40px] rounded-lg border px-3 py-2 text-sm font-semibold transition"
      :class="page === currentPage
        ? 'border-indigo-600 bg-indigo-600 text-white'
        : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
      :disabled="loading"
      @click="$emit('page-change', page)"
    >
      {{ page }}
    </button>

    <button
      class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
      :disabled="!hasNext || loading"
      @click="$emit('page-change', currentPage + 1)"
    >
      Siguiente
    </button>
  </nav>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  meta: {
    type: Object,
    default: () => ({ current_page: 1, last_page: 1 }),
  },
  links: {
    type: [Object, Array, null],
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['page-change']);

const currentPage = computed(() => Number(props.meta.current_page || 1));
const lastPage = computed(() => Number(props.meta.last_page || 1));
const hasPrev = computed(() => {
  if (Array.isArray(props.links)) {
    const prev = props.links.find((link) => String(link.label).toLowerCase().includes('previous'));
    return Boolean(prev?.url);
  }

  if (props.links && typeof props.links === 'object') {
    return Boolean(props.links.prev);
  }

  return currentPage.value > 1;
});

const hasNext = computed(() => {
  if (Array.isArray(props.links)) {
    const next = props.links.find((link) => String(link.label).toLowerCase().includes('next'));
    return Boolean(next?.url);
  }

  if (props.links && typeof props.links === 'object') {
    return Boolean(props.links.next);
  }

  return currentPage.value < lastPage.value;
});

const visiblePages = computed(() => {
  const maxButtons = 7;
  const total = lastPage.value;
  const current = currentPage.value;

  if (total <= maxButtons) {
    return Array.from({ length: total }, (_, i) => i + 1);
  }

  let start = Math.max(1, current - 3);
  let end = Math.min(total, start + maxButtons - 1);

  if (end - start < maxButtons - 1) {
    start = Math.max(1, end - maxButtons + 1);
  }

  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
});
</script>

<template>
  <header class="sticky top-[108px] z-30 border-b border-gray-200 bg-white/95 backdrop-blur-sm shadow-sm md:top-[92px]">
    <div class="container mx-auto space-y-3 px-4 py-3">
      <nav class="text-xs text-gray-500" aria-label="Breadcrumb">
        <a href="/" class="hover:text-gray-700">Inicio</a>
        <span class="mx-1">›</span>
        <a href="/marketplace" class="hover:text-gray-700">Marketplace</a>
        <span v-if="category" class="mx-1">›</span>
        <a v-if="category" :href="`/marketplace?category=${category}`" class="hover:text-gray-700">{{ category }}</a>
        <span v-if="subcategory" class="mx-1">›</span>
        <span v-if="subcategory" class="font-semibold text-gray-700">{{ subcategory }}</span>
      </nav>

      <div class="flex flex-wrap items-center gap-3">
        <p class="mr-auto text-sm md:text-base">
          <strong class="text-gray-900">{{ results }}</strong> resultados
          <span v-if="query" class="text-gray-500"> para "{{ query }}"</span>
        </p>

        <label class="text-sm text-gray-600">
          Ordenar por:
          <select v-model="localSort" class="ml-2 rounded-lg border border-gray-300 bg-white px-2 py-1 text-sm" @change="onSortChange">
            <option value="relevance">Mas relevantes</option>
            <option value="price_asc">Menor precio</option>
            <option value="price_desc">Mayor precio</option>
            <option value="newest">Mas nuevos</option>
            <option value="best_selling">Mas vendidos</option>
            <option value="best_rated">Mejor calificados</option>
          </select>
        </label>

        <div class="flex rounded-lg border border-gray-200 bg-gray-50 p-1">
          <button
            type="button"
            class="rounded px-2 py-1 text-sm"
            :class="viewMode === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'"
            aria-label="Vista cuadrícula"
            @click="$emit('view-change', 'grid')"
          >
            ⊞
          </button>
          <button
            type="button"
            class="rounded px-2 py-1 text-sm"
            :class="viewMode === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'"
            aria-label="Vista lista"
            @click="$emit('view-change', 'list')"
          >
            ☰
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const props = defineProps({
  results: { type: Number, default: 0 },
  query: { type: String, default: '' },
  category: { type: String, default: '' },
  subcategory: { type: String, default: '' },
  sort: { type: String, default: 'relevance' },
  viewMode: { type: String, default: 'grid' },
});

const emit = defineEmits(['sort-change', 'view-change']);
const route = useRoute();
const router = useRouter();

const localSort = ref(props.sort);

watch(() => props.sort, (value) => {
  localSort.value = value;
});

function onSortChange() {
  const sort = localSort.value;
  router.push({ query: { ...route.query, sort } });
  emit('sort-change', sort);
}
</script>

<template>
  <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm md:p-5">
    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
      <div class="xl:col-span-2">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Buscar</label>
        <input
          v-model="localFilters.search"
          type="text"
          placeholder="Nombre del producto"
          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
          @keyup.enter="applyFilters"
        >
      </div>

      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Categoría</label>
        <select
          v-model="localFilters.category_id"
          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
          @change="applyFilters"
        >
          <option value="">Todas</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Precio mínimo</label>
        <input
          v-model="localFilters.min_price"
          type="number"
          min="0"
          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
          @change="applyFilters"
        >
      </div>

      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Precio máximo</label>
        <input
          v-model="localFilters.max_price"
          type="number"
          min="0"
          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
          @change="applyFilters"
        >
      </div>
    </div>

    <div class="mt-3 flex flex-col gap-3 border-t border-gray-100 pt-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="w-full sm:w-64">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Ordenar</label>
        <select
          v-model="localFilters.sort"
          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
          @change="applyFilters"
        >
          <option value="newest">Más recientes</option>
          <option value="price_asc">Precio ascendente</option>
          <option value="price_desc">Precio descendente</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button
          class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
          type="button"
          :disabled="loading"
          @click="$emit('reset')"
        >
          Limpiar
        </button>
        <button
          class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:bg-indigo-300"
          type="button"
          :disabled="loading"
          @click="applyFilters"
        >
          Aplicar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
  filters: {
    type: Object,
    required: true,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['change', 'reset']);

const localFilters = reactive({ ...props.filters });

watch(
  () => props.filters,
  (newFilters) => {
    Object.assign(localFilters, newFilters);
  },
  { deep: true }
);

function applyFilters() {
  emit('change', { ...localFilters, page: 1 });
}
</script>

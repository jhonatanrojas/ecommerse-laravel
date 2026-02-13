<template>
  <aside class="mp-card sticky top-[170px] rounded-2xl p-4" aria-label="Filtros de busqueda">
    <h2 class="mb-3 text-base font-extrabold text-gray-900">Filtros</h2>
    <div v-if="activeChips.length" class="mb-4 border-b border-gray-100 pb-3">
      <div class="mb-2 flex items-center justify-between">
        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Filtros activos</p>
        <button type="button" class="text-xs font-semibold text-blue-600" @click="clearAll">Limpiar todo</button>
      </div>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="chip in activeChips"
          :key="chip.key"
          type="button"
          class="chip"
          @click="removeChip(chip)"
        >
          {{ chip.label }} ✕
        </button>
      </div>
    </div>

    <div class="space-y-5">
      <section>
        <h3 class="mb-2 text-sm font-bold text-gray-900">Categorias</h3>
        <div class="max-h-44 space-y-1 overflow-auto pr-1">
          <label v-for="category in categories" :key="category.id" class="filter-option">
            <input v-model="local.categories" type="checkbox" :value="String(category.id)">
            <span>{{ category.name }}</span>
            <span class="ml-auto text-xs text-gray-400">({{ category.product_count || 0 }})</span>
          </label>
        </div>
      </section>

      <section>
        <h3 class="mb-2 text-sm font-bold text-gray-900">Precio</h3>
        <div class="space-y-2">
          <input v-model.number="local.price_min" type="range" min="0" max="10000" class="w-full">
          <input v-model.number="local.price_max" type="range" min="0" max="10000" class="w-full">
          <div class="grid grid-cols-2 gap-2">
            <input v-model.number="local.price_min" type="number" min="0" max="10000" class="rounded-lg border border-gray-300 px-2 py-1 text-sm">
            <input v-model.number="local.price_max" type="number" min="0" max="10000" class="rounded-lg border border-gray-300 px-2 py-1 text-sm">
          </div>
          <p class="text-xs text-gray-500">${{ local.price_min }} - ${{ local.price_max }}</p>
        </div>
      </section>

      <section>
        <h3 class="mb-2 text-sm font-bold text-gray-900">Calificacion del vendedor</h3>
        <label v-for="option in ratingOptions" :key="option.value" class="filter-option">
          <input v-model="local.rating" type="radio" name="rating" :value="option.value">
          <span>{{ option.label }}</span>
          <span class="ml-auto text-xs text-gray-400">({{ option.count }})</span>
        </label>
      </section>

      <section>
        <h3 class="mb-2 text-sm font-bold text-gray-900">Tipo de envio</h3>
        <label class="filter-option">
          <input v-model="local.free_shipping" type="checkbox">
          <span>🚚 Envio gratis</span>
        </label>
        <label class="filter-option">
          <input v-model="local.fast_shipping" type="checkbox">
          <span>⚡ Envio rapido (24-48h)</span>
        </label>
      </section>

      <section>
        <h3 class="mb-2 text-sm font-bold text-gray-900">Ubicacion del vendedor</h3>
        <select v-model="local.location" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
          <option value="">Todas</option>
          <option value="near">Cerca de mi</option>
          <option value="city">Mi ciudad</option>
          <option value="state">Mi estado</option>
        </select>
      </section>

      <section>
        <h3 class="mb-2 text-sm font-bold text-gray-900">Condicion</h3>
        <label class="filter-option"><input v-model="local.condition" type="radio" name="condition" value="new"><span>Nuevo</span></label>
        <label class="filter-option"><input v-model="local.condition" type="radio" name="condition" value="used"><span>Usado</span></label>
      </section>
    </div>

    <div class="sticky bottom-0 mt-4 bg-white pt-3">
      <button type="button" class="mp-btn-primary w-full" @click="applyFilters">Aplicar filtros</button>
    </div>
  </aside>
</template>

<script setup>
import { computed, reactive, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['filters-changed', 'filters-applied']);

const route = useRoute();
const router = useRouter();

const ratingOptions = [
  { value: '5', label: '★★★★★', count: 43 },
  { value: '4', label: '★★★★+', count: 98 },
  { value: '3', label: '★★★+', count: 130 },
  { value: '2', label: '★★+', count: 201 },
];

const local = reactive(defaultFilters());

const activeChips = computed(() => {
  const chips = [];

  if (local.categories.length) chips.push({ key: 'categories', label: `${local.categories.length} categorias` });
  if (local.price_min > 0 || local.price_max < 10000) chips.push({ key: 'price', label: `$${local.price_min} - $${local.price_max}` });
  if (local.rating) chips.push({ key: 'rating', label: `${local.rating}+ estrellas` });
  if (local.free_shipping) chips.push({ key: 'free_shipping', label: 'Envio gratis' });
  if (local.fast_shipping) chips.push({ key: 'fast_shipping', label: 'Envio rapido' });
  if (local.location) chips.push({ key: 'location', label: `Ubicacion: ${local.location}` });
  if (local.condition) chips.push({ key: 'condition', label: local.condition === 'new' ? 'Nuevo' : 'Usado' });

  return chips;
});

watch(local, () => {
  emit('filters-changed', serializeFilters(local));
}, { deep: true });

watch(
  () => route.query,
  (query) => {
    hydrateFromQuery(query);
  },
  { immediate: true },
);

function defaultFilters() {
  return {
    categories: [],
    price_min: 0,
    price_max: 10000,
    rating: '',
    free_shipping: false,
    fast_shipping: false,
    location: '',
    condition: '',
  };
}

function hydrateFromQuery(query) {
  local.categories = query.categories ? String(query.categories).split(',').filter(Boolean) : [];
  local.price_min = Number(query.price_min ?? 0);
  local.price_max = Number(query.price_max ?? 10000);
  local.rating = query.rating ? String(query.rating) : '';
  local.free_shipping = query.free_shipping === '1';
  local.fast_shipping = query.fast_shipping === '1';
  local.location = query.location ? String(query.location) : '';
  local.condition = query.condition ? String(query.condition) : '';
}

function serializeFilters(state) {
  return {
    categories: state.categories,
    price_min: state.price_min,
    price_max: state.price_max,
    rating: state.rating,
    free_shipping: state.free_shipping,
    fast_shipping: state.fast_shipping,
    location: state.location,
    condition: state.condition,
  };
}

function applyFilters() {
  const payload = serializeFilters(local);
  const query = {
    ...route.query,
    categories: payload.categories.length ? payload.categories.join(',') : undefined,
    price_min: payload.price_min > 0 ? String(payload.price_min) : undefined,
    price_max: payload.price_max < 10000 ? String(payload.price_max) : undefined,
    rating: payload.rating || undefined,
    free_shipping: payload.free_shipping ? '1' : undefined,
    fast_shipping: payload.fast_shipping ? '1' : undefined,
    location: payload.location || undefined,
    condition: payload.condition || undefined,
  };

  router.push({ query });
  emit('filters-applied', payload);
}

function removeChip(chip) {
  if (chip.key === 'categories') local.categories = [];
  if (chip.key === 'price') {
    local.price_min = 0;
    local.price_max = 10000;
  }
  if (chip.key === 'rating') local.rating = '';
  if (chip.key === 'free_shipping') local.free_shipping = false;
  if (chip.key === 'fast_shipping') local.fast_shipping = false;
  if (chip.key === 'location') local.location = '';
  if (chip.key === 'condition') local.condition = '';

  applyFilters();
}

function clearAll() {
  Object.assign(local, defaultFilters());
  applyFilters();
}

defineExpose({ clearAll, applyFilters });
</script>

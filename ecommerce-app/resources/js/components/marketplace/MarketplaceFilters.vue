<template>
  <aside class="rounded-md border border-gray-200 bg-white p-4">
    <h3 class="text-sm font-extrabold uppercase tracking-wide text-gray-800">Filtros</h3>

    <div class="mt-4 space-y-4">
      <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Categoría</label>
        <select v-model="local.category_id" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
          <option value="">Todas</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
        </select>
      </div>

      <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Vendedor</label>
        <select v-model="local.vendor_uuid" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
          <option value="">Todos</option>
          <option v-for="vendor in vendors" :key="vendor.uuid" :value="vendor.uuid">{{ vendor.business_name }}</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div>
          <label class="mb-1 block text-xs font-semibold text-gray-600">Precio min</label>
          <input v-model="local.min_price" type="number" min="0" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="mb-1 block text-xs font-semibold text-gray-600">Precio max</label>
          <input v-model="local.max_price" type="number" min="0" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
      </div>

      <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Ubicación</label>
        <input v-model="local.location" type="text" placeholder="Ciudad o estado" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
      </div>

      <div class="flex gap-2">
        <button class="flex-1 rounded-md bg-amber-400 px-3 py-2 text-sm font-bold text-gray-900 hover:bg-amber-300" @click="apply">Aplicar</button>
        <button class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700" @click="reset">Limpiar</button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  categories: { type: Array, default: () => [] },
  vendors: { type: Array, default: () => [] },
});

const emit = defineEmits(['apply']);

const local = reactive({
  category_id: '',
  vendor_uuid: '',
  min_price: '',
  max_price: '',
  location: '',
});

watch(() => props.filters, (value) => {
  Object.assign(local, {
    category_id: value.category_id || '',
    vendor_uuid: value.vendor_uuid || '',
    min_price: value.min_price || '',
    max_price: value.max_price || '',
    location: value.location || '',
  });
}, { immediate: true, deep: true });

function apply() {
  emit('apply', { ...local, page: 1 });
}

function reset() {
  Object.assign(local, {
    category_id: '',
    vendor_uuid: '',
    min_price: '',
    max_price: '',
    location: '',
  });
  emit('apply', { ...local, page: 1 });
}
</script>

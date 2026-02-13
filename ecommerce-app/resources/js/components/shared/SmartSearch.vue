<template>
  <div ref="rootRef" class="relative w-full" :class="stickyOnMobile ? 'sticky top-0 z-[70] bg-white py-2 md:py-0' : ''">
    <label v-if="label" :for="inputId" class="sr-only">{{ label }}</label>
    <div class="relative">
      <input
        :id="inputId"
        ref="inputRef"
        v-model="query"
        type="search"
        class="input-search w-full"
        placeholder="Buscar productos, marcas y vendedores"
        role="combobox"
        aria-label="Buscar productos, marcas y vendedores"
        aria-autocomplete="list"
        :aria-expanded="String(isOpen)"
        :aria-controls="listboxId"
        @focus="onFocus"
        @keydown="onKeydown"
      >
      <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
        <svg v-if="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364 6.364-2.12-2.12M7.757 7.757 5.636 5.636m12.728 0-2.12 2.12M7.757 16.243l-2.12 2.12"/>
        </svg>
      </div>
    </div>

    <div
      v-if="isOpen"
      :id="listboxId"
      class="absolute left-0 right-0 z-[80] mt-2 max-h-[70vh] overflow-y-auto rounded-xl border border-gray-200 bg-white p-2 shadow-xl"
      role="listbox"
    >
      <template v-if="showHistory">
        <div class="mb-1 flex items-center justify-between px-2 py-1">
          <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Busquedas recientes</p>
          <button type="button" class="text-xs font-semibold text-blue-600 hover:text-blue-700" @click="clearHistory">Borrar historial</button>
        </div>
        <button
          v-for="(item, index) in history"
          :id="optionId(index)"
          :key="`history-${item}`"
          type="button"
          role="option"
          class="dropdown-suggestion flex w-full items-center justify-between"
          :class="activeIndex === index ? 'bg-blue-50' : ''"
          @mouseenter="activeIndex = index"
          @click="selectHistory(item)"
        >
          <span class="flex items-center gap-2 text-sm text-gray-700">
            <span aria-hidden="true">🕒</span>
            {{ item }}
          </span>
          <span
            class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700"
            role="button"
            tabindex="0"
            aria-label="Eliminar busqueda"
            @click.stop="removeHistory(item)"
            @keydown.enter.prevent="removeHistory(item)"
          >
            ✕
          </span>
        </button>
      </template>

      <template v-else>
        <section v-if="results.products.length">
          <p class="px-2 py-1 text-xs font-bold uppercase tracking-wide text-gray-500">Productos</p>
          <button
            v-for="product in results.products"
            :id="optionId(flatItems.indexOf(product))"
            :key="`p-${product.id}`"
            role="option"
            type="button"
            class="dropdown-suggestion flex w-full items-center gap-3"
            :class="activeIndex === flatItems.indexOf(product) ? 'bg-blue-50' : ''"
            @mouseenter="activeIndex = flatItems.indexOf(product)"
            @click="selectSuggestion('product', product)"
          >
            <img :src="product.thumbnail" :alt="product.name" class="h-10 w-10 rounded-md object-cover">
            <div class="min-w-0 text-left">
              <p class="truncate text-sm font-semibold text-gray-800" v-html="highlight(product.name)"></p>
              <p class="text-xs text-gray-500">{{ formatPrice(product.price) }}</p>
            </div>
          </button>
        </section>

        <section v-if="results.categories.length" class="mt-2 border-t border-gray-100 pt-2">
          <p class="px-2 py-1 text-xs font-bold uppercase tracking-wide text-gray-500">Categorias</p>
          <button
            v-for="category in results.categories"
            :id="optionId(flatItems.indexOf(category))"
            :key="`c-${category.id}`"
            role="option"
            type="button"
            class="dropdown-suggestion flex w-full items-center gap-3"
            :class="activeIndex === flatItems.indexOf(category) ? 'bg-blue-50' : ''"
            @mouseenter="activeIndex = flatItems.indexOf(category)"
            @click="selectSuggestion('category', category)"
          >
            <span class="text-lg" aria-hidden="true">📁</span>
            <div class="min-w-0 text-left">
              <p class="truncate text-sm font-semibold text-gray-800" v-html="highlight(category.name)"></p>
              <p class="text-xs text-gray-500">{{ category.product_count || 0 }} productos</p>
            </div>
          </button>
        </section>

        <section v-if="results.sellers.length" class="mt-2 border-t border-gray-100 pt-2">
          <p class="px-2 py-1 text-xs font-bold uppercase tracking-wide text-gray-500">Vendedores</p>
          <button
            v-for="seller in results.sellers"
            :id="optionId(flatItems.indexOf(seller))"
            :key="`s-${seller.id}`"
            role="option"
            type="button"
            class="dropdown-suggestion flex w-full items-center gap-3"
            :class="activeIndex === flatItems.indexOf(seller) ? 'bg-blue-50' : ''"
            @mouseenter="activeIndex = flatItems.indexOf(seller)"
            @click="selectSuggestion('seller', seller)"
          >
            <img :src="seller.logo" :alt="seller.name" class="h-10 w-10 rounded-full border border-gray-200 object-cover">
            <div class="min-w-0 text-left">
              <p class="truncate text-sm font-semibold text-gray-800" v-html="highlight(seller.name)"></p>
              <p class="text-xs text-gray-500">{{ stars(seller.rating) }} {{ Number(seller.rating || 0).toFixed(1) }}</p>
            </div>
          </button>
        </section>

        <button
          type="button"
          class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-left text-sm font-semibold text-blue-700 hover:bg-blue-50"
          @click="submitSearch"
        >
          Ver todos los resultados
        </button>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { useDebounceFn, useEventListener } from '@vueuse/core';
import api from '../../services/api';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  stickyOnMobile: {
    type: Boolean,
    default: false,
  },
  label: {
    type: String,
    default: 'Buscar',
  },
});

const emit = defineEmits(['update:modelValue', 'search', 'navigate']);

const STORAGE_KEY = 'smart-search-history';
const inputId = 'smart-search-input';
const listboxId = 'smart-search-listbox';

const rootRef = ref(null);
const inputRef = ref(null);
const query = ref(props.modelValue);
const loading = ref(false);
const isOpen = ref(false);
const activeIndex = ref(-1);
const requestController = ref(null);

const results = ref({ products: [], categories: [], sellers: [] });
const history = ref(loadHistory());

const showHistory = computed(() => isOpen.value && !query.value.trim() && history.value.length > 0);

const flatItems = computed(() => {
  if (showHistory.value) {
    return history.value.map((item) => ({ type: 'history', value: item }));
  }

  return [
    ...results.value.products.map((item) => ({ ...item, __type: 'product' })),
    ...results.value.categories.map((item) => ({ ...item, __type: 'category' })),
    ...results.value.sellers.map((item) => ({ ...item, __type: 'seller' })),
  ];
});

watch(() => props.modelValue, (value) => {
  query.value = value || '';
});

watch(query, (value) => {
  emit('update:modelValue', value);
  debouncedSearch(value);
});

const debouncedSearch = useDebounceFn(async (value) => {
  const text = (value || '').trim();

  if (text.length < 2) {
    results.value = { products: [], categories: [], sellers: [] };
    loading.value = false;
    activeIndex.value = -1;
    return;
  }

  if (requestController.value) {
    requestController.value.abort();
  }

  requestController.value = new AbortController();
  loading.value = true;

  try {
    const response = await api.get('/search/autocomplete', {
      params: { q: text },
      signal: requestController.value.signal,
    });

    results.value = {
      products: response.data?.products || [],
      categories: response.data?.categories || [],
      sellers: response.data?.sellers || [],
    };

    isOpen.value = true;
    activeIndex.value = flatItems.value.length ? 0 : -1;
  } catch (error) {
    if (error.name !== 'CanceledError' && error.code !== 'ERR_CANCELED') {
      results.value = { products: [], categories: [], sellers: [] };
    }
  } finally {
    loading.value = false;
  }
}, 300);

useEventListener(document, 'click', (event) => {
  if (!rootRef.value?.contains(event.target)) {
    isOpen.value = false;
    activeIndex.value = -1;
  }
});

function onFocus() {
  isOpen.value = true;
  activeIndex.value = showHistory.value ? 0 : activeIndex.value;
}

function onKeydown(event) {
  if (!isOpen.value && ['ArrowDown', 'ArrowUp'].includes(event.key)) {
    isOpen.value = true;
  }

  if (event.key === 'ArrowDown') {
    event.preventDefault();
    moveSelection(1);
    return;
  }

  if (event.key === 'ArrowUp') {
    event.preventDefault();
    moveSelection(-1);
    return;
  }

  if (event.key === 'Enter') {
    event.preventDefault();
    const item = flatItems.value[activeIndex.value];

    if (showHistory.value && item?.value) {
      selectHistory(item.value);
      return;
    }

    if (item?.__type) {
      selectSuggestion(item.__type, item);
      return;
    }

    submitSearch();
    return;
  }

  if (event.key === 'Escape') {
    isOpen.value = false;
    activeIndex.value = -1;
  }

  if (event.key === 'Tab') {
    isOpen.value = false;
  }
}

function moveSelection(delta) {
  const total = flatItems.value.length;
  if (!total) return;

  const next = activeIndex.value + delta;
  if (next < 0) {
    activeIndex.value = total - 1;
  } else if (next >= total) {
    activeIndex.value = 0;
  } else {
    activeIndex.value = next;
  }

  const id = optionId(activeIndex.value);
  document.getElementById(id)?.scrollIntoView({ block: 'nearest' });
}

function submitSearch() {
  const text = query.value.trim();
  if (!text) return;

  pushHistory(text);
  isOpen.value = false;
  emit('search', text);
}

function selectHistory(item) {
  query.value = item;
  submitSearch();
}

function selectSuggestion(type, item) {
  pushHistory(query.value.trim());
  isOpen.value = false;

  if (type === 'product') {
    emit('navigate', `/marketplace/products/${item.slug}`);
    return;
  }

  if (type === 'category') {
    emit('navigate', `/marketplace?category=${item.slug}`);
    return;
  }

  if (type === 'seller') {
    emit('navigate', `/marketplace/vendors/${item.slug}`);
  }
}

function loadHistory() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    const parsed = raw ? JSON.parse(raw) : [];
    return Array.isArray(parsed) ? parsed.slice(0, 10) : [];
  } catch {
    return [];
  }
}

function pushHistory(text) {
  if (!text) return;

  const unique = [text, ...history.value.filter((item) => item.toLowerCase() !== text.toLowerCase())].slice(0, 10);
  history.value = unique;
  localStorage.setItem(STORAGE_KEY, JSON.stringify(unique));
}

function removeHistory(item) {
  history.value = history.value.filter((entry) => entry !== item);
  localStorage.setItem(STORAGE_KEY, JSON.stringify(history.value));
}

function clearHistory() {
  history.value = [];
  localStorage.removeItem(STORAGE_KEY);
}

function optionId(index) {
  return `smart-option-${index}`;
}

function escapeRegExp(text) {
  return text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function highlight(text = '') {
  const q = query.value.trim();
  if (!q) return text;

  const regex = new RegExp(`(${escapeRegExp(q)})`, 'ig');
  return String(text).replace(regex, '<mark class="bg-yellow-200 text-gray-900">$1</mark>');
}

function formatPrice(price) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(price || 0));
}

function stars(rating) {
  const rounded = Math.round(Number(rating || 0));
  return `${'★'.repeat(rounded)}${'☆'.repeat(Math.max(0, 5 - rounded))}`;
}

onBeforeUnmount(() => {
  if (requestController.value) {
    requestController.value.abort();
  }
});

defineExpose({ focus: () => inputRef.value?.focus() });
</script>

<template>
  <div class="sticky top-0 z-40 border-b border-amber-200 bg-amber-300/95 backdrop-blur-sm">
    <div class="container mx-auto px-4 py-3">
      <SmartSearch
        v-model="localQuery"
        sticky-on-mobile
        @search="emitSearch"
        @navigate="goTo"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import SmartSearch from '../shared/SmartSearch.vue';

const props = defineProps({
  query: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['search']);

const localQuery = ref(props.query);

watch(() => props.query, (value) => {
  localQuery.value = value;
});

function emitSearch() {
  emit('search', localQuery.value.trim());
}

function goTo(path) {
  if (!path) return;
  window.location.href = path;
}
</script>

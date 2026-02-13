<template>
  <Teleport to="body">
    <transition name="fade">
      <div v-if="open" class="fixed inset-0 z-[100] bg-black/50" @click="close"></div>
    </transition>

    <transition name="sheet">
      <section
        v-if="open"
        class="fixed inset-x-0 bottom-0 z-[110] max-h-[78vh] rounded-t-2xl bg-white shadow-2xl"
        @keydown.esc="close"
      >
        <button class="mx-auto mt-2 block h-1.5 w-14 rounded-full bg-gray-300" aria-label="Cerrar filtros" @click="close"></button>

        <header class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
          <h2 class="text-lg font-bold text-gray-900">Filtros</h2>
          <button class="text-sm font-semibold text-blue-600" @click="clearAll">Limpiar todo</button>
        </header>

        <div
          class="overflow-y-auto px-4 py-3"
          style="max-height: calc(78vh - 124px)"
          @touchstart.passive="onTouchStart"
          @touchend.passive="onTouchEnd"
        >
          <FilterSidebar ref="sidebarRef" :categories="categories" @filters-applied="onApplied" @filters-changed="$emit('filters-changed', $event)" />
        </div>

        <footer class="sticky bottom-0 border-t border-gray-200 bg-white p-4">
          <button class="mp-btn-primary w-full" @click="applyAndClose">Ver {{ results }} resultados</button>
        </footer>
      </section>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import FilterSidebar from './FilterSidebar.vue';

const props = defineProps({
  open: { type: Boolean, default: false },
  results: { type: Number, default: 0 },
  categories: { type: Array, default: () => [] },
});

const emit = defineEmits(['close', 'filters-applied', 'filters-changed']);

const sidebarRef = ref(null);
const touchY = ref(0);

watch(
  () => props.open,
  (value) => {
    document.body.style.overflow = value ? 'hidden' : '';
  },
  { immediate: true },
);

function applyAndClose() {
  sidebarRef.value?.applyFilters?.();
  close();
}

function clearAll() {
  sidebarRef.value?.clearAll?.();
}

function onApplied(payload) {
  emit('filters-applied', payload);
}

function close() {
  emit('close');
}

function onTouchStart(event) {
  touchY.value = event.changedTouches?.[0]?.clientY || 0;
}

function onTouchEnd(event) {
  const end = event.changedTouches?.[0]?.clientY || 0;
  if (end - touchY.value > 80) {
    close();
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.sheet-enter-active,
.sheet-leave-active {
  transition: transform 0.3s ease;
}

.sheet-enter-from,
.sheet-leave-to {
  transform: translateY(100%);
}
</style>

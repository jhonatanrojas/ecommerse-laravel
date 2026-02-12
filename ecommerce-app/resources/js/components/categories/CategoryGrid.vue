<template>
  <div>
    <div v-if="loading" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <div
        v-for="n in 8"
        :key="`category-skeleton-${n}`"
        class="overflow-hidden rounded-2xl border border-gray-100 bg-white p-3"
      >
        <div class="skeleton aspect-[4/3] w-full rounded-xl" />
        <div class="mt-4 space-y-2">
          <div class="skeleton h-5 w-7/12" />
          <div class="skeleton h-4 w-4/12" />
          <div class="skeleton h-4 w-5/12" />
        </div>
      </div>
    </div>

    <div v-else-if="categories.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <CategoryCard
        v-for="category in categories"
        :key="category.id"
        :category="category"
      />
    </div>

    <div v-else class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
      <p class="text-sm text-gray-500">No hay categorías disponibles.</p>
    </div>
  </div>
</template>

<script setup>
import CategoryCard from './CategoryCard.vue';

defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
});
</script>

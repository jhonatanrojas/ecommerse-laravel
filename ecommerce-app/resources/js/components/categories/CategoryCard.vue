<template>
  <article class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
    <a :href="`/categories/${category.slug}`" class="block">
      <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
        <img
          v-if="category.image"
          :src="category.image"
          :alt="category.name"
          class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
          loading="lazy"
        >
        <div v-else class="flex h-full items-center justify-center text-gray-300">
          <svg class="h-14 w-14" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm-7 9a7 7 0 1 1 14 0H5Z" />
          </svg>
        </div>
      </div>

      <div class="p-4">
        <h3 class="text-lg font-bold text-gray-900 transition-colors group-hover:text-indigo-700">
          {{ category.name }}
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ category.productCount }} productos
        </p>
        <p class="mt-3 inline-flex items-center text-sm font-semibold text-indigo-600">
          Ver productos
          <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
        </p>
      </div>
    </a>
  </article>
</template>

<script setup>
defineProps({
  category: {
    type: Object,
    required: true,
  },
});
</script>

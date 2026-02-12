<template>
  <header class="mb-8 text-center md:mb-10">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Categoría</p>
    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">{{ title }}</h1>
    <p v-if="description" class="mx-auto mt-3 max-w-3xl text-sm text-gray-500 md:text-base">
      {{ description }}
    </p>
  </header>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
});
</script>

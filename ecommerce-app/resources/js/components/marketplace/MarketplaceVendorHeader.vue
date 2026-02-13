<template>
  <section class="rounded-md border border-gray-200 bg-white p-5">
    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tienda oficial</p>
    <h1 class="mt-1 text-2xl font-black tracking-tight text-gray-900">{{ vendor?.business_name }}</h1>
    <p class="mt-2 text-sm text-gray-600">{{ vendor?.address || 'Ubicación no especificada' }}</p>
    <p class="mt-1 text-sm text-gray-500">Contacto: {{ vendor?.email || 'N/A' }}</p>
  </section>
</template>

<script setup>
defineProps({
  vendor: {
    type: Object,
    default: null,
  },
});
</script>

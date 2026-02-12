<template>
  <div class="mb-5">
    <div class="flex items-center justify-between gap-3">
      <div>
        <p v-if="eyebrow" class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">
          {{ eyebrow }}
        </p>
        <h2 class="text-xl font-bold tracking-tight text-gray-900">
          {{ title }}
        </h2>
        <p v-if="subtitle" class="mt-1 text-sm text-gray-500">
          {{ subtitle }}
        </p>
      </div>
      <slot name="action"></slot>
    </div>
    <div class="mt-4 h-px w-full bg-gray-100"></div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  eyebrow: {
    type: String,
    default: '',
  },
});
</script>

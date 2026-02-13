<template>
  <button
    class="inline-flex h-12 items-center justify-center rounded-md bg-amber-400 px-6 text-sm font-black text-gray-900 transition hover:bg-amber-300 disabled:cursor-not-allowed disabled:bg-gray-300"
    :disabled="loading"
    @click="$emit('buy')"
  >
    <span v-if="loading" class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-gray-700 border-t-transparent" />
    Comprar ahora
  </button>
</template>

<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['buy']);
</script>

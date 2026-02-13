<template>
  <div class="mp-card p-3">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
      <img v-if="active" :src="active" :alt="alt" class="h-[460px] w-full object-cover">
      <div v-else class="flex h-[460px] items-center justify-center text-gray-300">Sin imagen</div>
    </div>
    <div v-if="images.length > 1" class="mt-3 grid grid-cols-5 gap-2 md:grid-cols-6">
      <button
        v-for="image in images"
        :key="image.id"
        class="overflow-hidden rounded border transition"
        :class="active === image.url ? 'border-[var(--mp-accent)] ring-2 ring-[var(--mp-accent-soft)]' : 'border-gray-200 hover:border-gray-300'"
        @click="active = image.url"
      >
        <img :src="image.url" :alt="alt" class="h-16 w-full object-cover">
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  images: { type: Array, default: () => [] },
  alt: { type: String, default: '' },
});

const active = ref(props.images?.[0]?.url || null);

watch(() => props.images, (value) => {
  active.value = value?.[0]?.url || null;
}, { immediate: true });
</script>

<template>
  <div class="inline-flex items-center gap-2 rounded-lg bg-black/25 px-3 py-2 text-sm font-semibold text-white" role="status" aria-live="polite">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l2.5 2.5M12 3.75A8.25 8.25 0 1 1 3.75 12 8.25 8.25 0 0 1 12 3.75Z"/>
    </svg>
    <span>{{ display }}</span>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
  endTime: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(['expired']);

const secondsLeft = ref(0);
let timer = null;
let expiredEmitted = false;

const display = computed(() => {
  const total = Math.max(0, secondsLeft.value);
  const hh = String(Math.floor(total / 3600)).padStart(2, '0');
  const mm = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
  const ss = String(total % 60).padStart(2, '0');
  return `${hh}:${mm}:${ss}`;
});

function refreshRemaining() {
  const end = dayjs(props.endTime);
  const now = dayjs();
  const diff = end.diff(now, 'second');

  secondsLeft.value = Math.max(0, diff);

  if (secondsLeft.value === 0 && !expiredEmitted) {
    expiredEmitted = true;
    emit('expired');
  }
}

function startTimer() {
  clearInterval(timer);
  expiredEmitted = false;
  refreshRemaining();

  timer = setInterval(() => {
    refreshRemaining();
    if (secondsLeft.value === 0) {
      clearInterval(timer);
    }
  }, 1000);
}

watch(() => props.endTime, startTimer);

onMounted(startTimer);

onBeforeUnmount(() => {
  clearInterval(timer);
});
</script>

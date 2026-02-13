import { onMounted, onBeforeUnmount } from 'vue';

export function useLazyLoad(instance) {
  onMounted(() => {
    instance?.update?.();
  });

  onBeforeUnmount(() => {
    // vanilla-lazyload does not require per-component cleanup when global instance is shared.
  });
}

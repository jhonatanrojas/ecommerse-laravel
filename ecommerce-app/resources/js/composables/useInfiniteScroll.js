import { onBeforeUnmount, onMounted, ref } from 'vue';

export function useInfiniteScroll(callback, options = {}) {
  const { threshold = 300, initialLoad = true } = options;

  const isLoading = ref(false);
  const hasMore = ref(true);
  const sentinel = ref(null);
  let observer = null;

  async function loadMore() {
    if (isLoading.value || !hasMore.value) return;

    isLoading.value = true;
    try {
      const result = await callback();
      hasMore.value = Boolean(result?.hasMore);
      if (!hasMore.value && observer && sentinel.value) {
        observer.unobserve(sentinel.value);
      }
    } finally {
      isLoading.value = false;
    }
  }

  onMounted(async () => {
    if (initialLoad) {
      await loadMore();
    }

    observer = new IntersectionObserver(
      (entries) => {
        const [entry] = entries;
        if (entry?.isIntersecting) {
          loadMore();
        }
      },
      { rootMargin: `${threshold}px` },
    );

    if (sentinel.value) {
      observer.observe(sentinel.value);
    }
  });

  onBeforeUnmount(() => {
    if (observer && sentinel.value) {
      observer.unobserve(sentinel.value);
    }
  });

  return {
    sentinel,
    isLoading,
    hasMore,
    loadMore,
  };
}

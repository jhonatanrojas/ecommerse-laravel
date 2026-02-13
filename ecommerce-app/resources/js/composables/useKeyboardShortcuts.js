import { onBeforeUnmount, onMounted } from 'vue';

export function useKeyboardShortcuts({ onSearchFocus, onOpenFilters, onEscape } = {}) {
  function handler(event) {
    const key = event.key;
    const isMac = navigator.platform.toUpperCase().includes('MAC');
    const cmdOrCtrl = isMac ? event.metaKey : event.ctrlKey;

    if (key === '/' && !event.target?.matches('input, textarea, [contenteditable=true]')) {
      event.preventDefault();
      onSearchFocus?.();
      return;
    }

    if (cmdOrCtrl && key.toLowerCase() === 'k') {
      event.preventDefault();
      onOpenFilters?.();
      return;
    }

    if (key === 'Escape') {
      onEscape?.();
    }
  }

  onMounted(() => {
    window.addEventListener('keydown', handler);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('keydown', handler);
  });
}

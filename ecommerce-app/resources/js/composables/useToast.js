import { ref } from 'vue';

const toastContainer = ref(null);

export function useToast() {
  const setToastContainer = (container) => {
    toastContainer.value = container;
  };

  const showToast = ({ title, message = '', type = 'info', duration = 5000 }) => {
    if (toastContainer.value) {
      toastContainer.value.addToast({ title, message, type, duration });
    } else {
      console.warn('Toast container not initialized');
    }
  };

  const success = (title, message = '', duration = 5000) => {
    showToast({ title, message, type: 'success', duration });
  };

  const error = (title, message = '', duration = 5000) => {
    showToast({ title, message, type: 'error', duration });
  };

  const warning = (title, message = '', duration = 5000) => {
    showToast({ title, message, type: 'warning', duration });
  };

  const info = (title, message = '', duration = 5000) => {
    showToast({ title, message, type: 'info', duration });
  };

  return {
    setToastContainer,
    showToast,
    success,
    error,
    warning,
    info,
  };
}

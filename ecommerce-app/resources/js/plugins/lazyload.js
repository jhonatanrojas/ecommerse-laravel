import LazyLoad from 'vanilla-lazyload';

let lazyInstance = null;

export function initLazyLoad() {
  if (lazyInstance) return lazyInstance;

  lazyInstance = new LazyLoad({
    elements_selector: '.lazyload',
    threshold: 300,
    callback_loaded: (el) => {
      el.classList.add('loaded');
    },
    callback_error: (el) => {
      el.classList.add('error');
    },
  });

  return lazyInstance;
}

export function getLazyLoadInstance() {
  return lazyInstance;
}

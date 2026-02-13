import { nextTick } from 'vue';
import { mount } from '@vue/test-utils';
import { useInfiniteScroll } from '../../resources/js/composables/useInfiniteScroll';

class MockIntersectionObserver {
  constructor(callback) {
    this.callback = callback;
    this.elements = [];
  }

  observe(el) {
    this.elements.push(el);
    this.callback([{ isIntersecting: true, target: el }]);
  }

  unobserve() {}
}

describe('useInfiniteScroll', () => {
  beforeEach(() => {
    global.IntersectionObserver = MockIntersectionObserver;
  });

  it('carga mas al llegar threshold y evita duplicados', async () => {
    const callback = vi.fn()
      .mockResolvedValueOnce({ hasMore: true })
      .mockResolvedValueOnce({ hasMore: false });

    const wrapper = mount({
      template: '<div><div ref="target"></div></div>',
      setup() {
        const { sentinel, loadMore, hasMore } = useInfiniteScroll(callback, { initialLoad: false });
        return { sentinel, loadMore, hasMore };
      },
    });

    wrapper.vm.sentinel = wrapper.find('div').element;
    await wrapper.vm.loadMore();
    await wrapper.vm.loadMore();

    expect(callback).toHaveBeenCalledTimes(2);
    expect(wrapper.vm.hasMore).toBe(false);
  });

  it('detiene carga cuando hasMore=false', async () => {
    const callback = vi.fn().mockResolvedValue({ hasMore: false });

    const wrapper = mount({
      template: '<div><div ref="target"></div></div>',
      setup() {
        const { loadMore, hasMore } = useInfiniteScroll(callback, { initialLoad: false });
        return { loadMore, hasMore };
      },
    });

    await wrapper.vm.loadMore();
    await nextTick();
    await wrapper.vm.loadMore();

    expect(callback).toHaveBeenCalledTimes(1);
  });
});

import { mount } from '@vue/test-utils';
import SmartSearch from '../../resources/js/components/shared/SmartSearch.vue';

const getMock = vi.fn();

vi.mock('../../resources/js/services/api', () => ({
  default: {
    get: (...args) => getMock(...args),
  },
}));

describe('SmartSearch', () => {
  beforeEach(() => {
    vi.useFakeTimers();
    localStorage.clear();
    getMock.mockReset();
    getMock.mockResolvedValue({
      data: {
        products: [{ id: 1, name: 'Laptop Pro', slug: 'laptop-pro', price: 1200, thumbnail: 't.jpg' }],
        categories: [{ id: 2, name: 'Laptops', slug: 'laptops', product_count: 15 }],
        sellers: [{ id: 3, name: 'Tech Store', slug: 'tech-store', logo: 'l.jpg', rating: 4.8 }],
      },
    });
  });

  it('aplica debounce para llamadas API', async () => {
    const wrapper = mount(SmartSearch);
    await wrapper.find('input').setValue('la');
    await wrapper.find('input').setValue('lap');
    vi.advanceTimersByTime(299);
    expect(getMock).not.toHaveBeenCalled();
    vi.advanceTimersByTime(2);
    expect(getMock).toHaveBeenCalledTimes(1);
  });

  it('navegacion por teclado (down/up/enter/esc)', async () => {
    const wrapper = mount(SmartSearch);
    const input = wrapper.find('input');

    await input.setValue('la');
    vi.runAllTimers();
    await input.trigger('keydown', { key: 'ArrowDown' });
    await input.trigger('keydown', { key: 'Enter' });
    expect(wrapper.emitted('navigate') || wrapper.emitted('search')).toBeTruthy();
    await input.trigger('keydown', { key: 'Escape' });
    expect(wrapper.find('[role="listbox"]').exists()).toBe(false);
  });

  it('guarda/carga historial y cierra con click fuera', async () => {
    const wrapper = mount(SmartSearch);
    const input = wrapper.find('input');
    await input.setValue('iphone');
    vi.runAllTimers();
    await input.trigger('keydown', { key: 'Enter' });

    const wrapper2 = mount(SmartSearch);
    await wrapper2.find('input').trigger('focus');
    expect(localStorage.getItem('smart-search-history')).toContain('iphone');

    document.body.click();
    await wrapper2.vm.$nextTick();
    expect(wrapper2.find('[role="listbox"]').exists()).toBe(false);
  });

  it('highlight de matches', async () => {
    const wrapper = mount(SmartSearch);
    const input = wrapper.find('input');
    await input.trigger('focus');
    await input.setValue('lap');
    vi.runAllTimers();
    await Promise.resolve();
    await wrapper.vm.$nextTick();
    expect(wrapper.html()).toContain('<mark');
  });
});

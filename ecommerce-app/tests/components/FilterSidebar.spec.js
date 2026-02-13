import { mount } from '@vue/test-utils';
import FilterSidebar from '../../resources/js/components/marketplace/FilterSidebar.vue';

const push = vi.fn();

vi.mock('vue-router', async () => {
  const actual = await vi.importActual('vue-router');
  return {
    ...actual,
    useRoute: () => ({ query: {} }),
    useRouter: () => ({ push }),
  };
});

describe('FilterSidebar', () => {
  beforeEach(() => {
    push.mockClear();
  });

  it('actualiza URL al aplicar filtros', async () => {
    const wrapper = mount(FilterSidebar, {
      props: {
        categories: [{ id: 1, name: 'Electronics', product_count: 10 }],
      },
    });

    await wrapper.find('input[type="checkbox"]').setValue(true);
    await wrapper.find('button.mp-btn-primary').trigger('click');
    expect(push).toHaveBeenCalled();
  });

  it('limpia todos los filtros', async () => {
    const wrapper = mount(FilterSidebar, {
      props: { categories: [{ id: 1, name: 'Electronics', product_count: 10 }] },
    });

    await wrapper.find('input[type="checkbox"]').setValue(true);
    await wrapper.find('button.mp-btn-primary').trigger('click');
    await wrapper.find('button.text-blue-600').trigger('click');
    expect(push).toHaveBeenCalledTimes(2);
  });

  it('crea chips activos y permite remover', async () => {
    const wrapper = mount(FilterSidebar, {
      props: { categories: [{ id: 1, name: 'Electronics', product_count: 10 }] },
    });

    await wrapper.find('input[type="checkbox"]').setValue(true);
    await wrapper.find('button.mp-btn-primary').trigger('click');
    expect(wrapper.findAll('.chip').length).toBeGreaterThan(0);
    await wrapper.find('.chip').trigger('click');
    expect(push).toHaveBeenCalledTimes(2);
  });
});

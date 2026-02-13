import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import ProductCard from '../../resources/js/components/products/ProductCard.vue';

vi.mock('../../resources/js/services/api', () => ({
  default: {
    post: vi.fn(() => Promise.resolve({ data: {} })),
    delete: vi.fn(() => Promise.resolve({ data: {} })),
  },
}));

const baseProduct = {
  id: 1,
  uuid: 'uuid-1',
  name: 'Producto demo',
  slug: 'producto-demo',
  image: 'https://example.com/p.jpg',
  price: 99.9,
  compare_price: 129.9,
  rating: 4.2,
  sales_count: 25,
  stock: 3,
  low_stock: true,
  is_hot: true,
  is_new: true,
  discount_percentage: 20,
  free_shipping: true,
  estimated_delivery: 'Llega manana',
  installments: { count: 6, amount: 16.65 },
  seller: { id: 10, name: 'Seller', logo: 'https://example.com/s.jpg', verified: true },
};

describe('ProductCard', () => {
  it('renderiza informacion base', () => {
    const wrapper = mount(ProductCard, { props: { product: baseProduct } });
    expect(wrapper.text()).toContain('Producto demo');
    expect(wrapper.text()).toContain('ventas');
    expect(wrapper.text()).toContain('Envio gratis');
  });

  it('emite add-to-cart', async () => {
    const wrapper = mount(ProductCard, { props: { product: baseProduct } });
    await wrapper.find('button.btn-primary').trigger('click');
    expect(wrapper.emitted('add-to-cart')).toBeTruthy();
  });

  it('toggle wishlist funciona', async () => {
    const wrapper = mount(ProductCard, { props: { product: baseProduct } });
    const btn = wrapper.find('button[aria-label="Agregar a favoritos"]');
    await btn.trigger('click');
    await nextTick();
    expect(wrapper.emitted('wishlist-change')).toBeTruthy();
  });

  it('quick view existe en overlay desktop', () => {
    const wrapper = mount(ProductCard, { props: { product: baseProduct } });
    expect(wrapper.text()).toContain('Vista Rapida');
  });

  it('muestra badges y warning de stock', () => {
    const wrapper = mount(ProductCard, { props: { product: baseProduct } });
    expect(wrapper.text()).toContain('HOT');
    expect(wrapper.text()).toContain('NUEVO');
    expect(wrapper.text()).toContain('Solo quedan');
  });
});

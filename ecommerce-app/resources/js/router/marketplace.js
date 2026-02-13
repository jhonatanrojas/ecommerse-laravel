import { createRouter, createWebHistory } from 'vue-router';
import MarketplaceMainPage from '../Pages/Marketplace/MarketplaceMainPage.vue';
import MarketplaceVendorPage from '../Pages/Marketplace/MarketplaceVendorPage.vue';
import MarketplaceProductPage from '../Pages/Marketplace/MarketplaceProductPage.vue';
import MarketplaceMessagesPage from '../Pages/Marketplace/MarketplaceMessagesPage.vue';

const routes = [
  { path: '/marketplace', name: 'marketplace', component: MarketplaceMainPage },
  { path: '/marketplace/search', name: 'marketplace.search', component: MarketplaceMainPage },
  { path: '/marketplace/vendors/:slug', name: 'marketplace.vendor', component: MarketplaceVendorPage, props: true },
  { path: '/marketplace/products/:slug', name: 'marketplace.product', component: MarketplaceProductPage, props: true },
  { path: '/messages/:orderUuid', name: 'marketplace.messages', component: MarketplaceMessagesPage, props: true },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.afterEach((to) => {
  if (to.name === 'marketplace.product') {
    document.title = 'Detalle del producto - Marketplace';
  } else if (to.name === 'marketplace.vendor') {
    document.title = 'Perfil del vendedor - Marketplace';
  } else if (to.name === 'marketplace.messages') {
    document.title = 'Mensajería - Marketplace';
  } else {
    document.title = 'Marketplace';
  }
});

export default router;

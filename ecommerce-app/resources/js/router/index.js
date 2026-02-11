import { createRouter, createWebHistory } from 'vue-router';
import CheckoutPage from '../Pages/CheckoutPage.vue';
import OrderSuccess from '../Pages/OrderSuccess.vue';

const routes = [
  {
    path: '/checkout',
    name: 'checkout',
    component: CheckoutPage,
    meta: {
      title: 'Checkout - Finalizar Compra',
      requiresCart: true,
    },
  },
  {
    path: '/order-success/:orderId?',
    name: 'order-success',
    component: OrderSuccess,
    meta: {
      title: 'Pedido Exitoso',
    },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
  // Set page title
  if (to.meta.title) {
    document.title = to.meta.title;
  }

  // Check if route requires cart
  if (to.meta.requiresCart) {
    // You can add logic here to check if cart is not empty
    // For now, we'll let the component handle it
  }

  next();
});

export default router;

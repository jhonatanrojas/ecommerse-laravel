import { createRouter, createWebHistory } from 'vue-router';
import { requireAuth } from '../middleware/auth';

import CustomerDashboard from '../Pages/Customer/CustomerDashboard.vue';
import CustomerOrdersPage from '../Pages/Customer/CustomerOrdersPage.vue';
import CustomerOrderDetailPage from '../Pages/Customer/CustomerOrderDetailPage.vue';

const authGuard = async () => {
  return await requireAuth('/customer');
};

const routes = [
  {
    path: '/customer',
    name: 'customer.dashboard',
    component: CustomerDashboard,
    meta: {
      title: 'Mi cuenta',
    },
    beforeEnter: authGuard,
  },
  {
    path: '/customer/orders',
    name: 'customer.orders',
    component: CustomerOrdersPage,
    meta: {
      title: 'Mis órdenes',
    },
    beforeEnter: authGuard,
  },
  {
    path: '/customer/orders/:id',
    name: 'customer.order.detail',
    component: CustomerOrderDetailPage,
    meta: {
      title: 'Detalle de orden',
    },
    beforeEnter: authGuard,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    document.title = to.meta.title;
  }
  next();
});

export default router;

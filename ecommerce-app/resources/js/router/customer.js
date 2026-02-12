import { createRouter, createWebHistory } from 'vue-router';
import { requireAuth } from '../middleware/auth';

import CustomerDashboard from '../Pages/Customer/CustomerDashboard.vue';

const routes = [
  {
    path: '/customer',
    name: 'customer.dashboard',
    component: CustomerDashboard,
    meta: {
      title: 'Mi cuenta',
    },
    beforeEnter: async () => {
      return await requireAuth('/customer');
    },
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


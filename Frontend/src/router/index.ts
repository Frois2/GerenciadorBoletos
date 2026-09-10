import { createRouter, createWebHistory } from 'vue-router';

import ListarCliente from '@/views/ListarCliente.vue';
import ListarBoleto from '@/views/ListarBoleto.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/boletos',
    },
    {
      path: '/clientes',
      component: ListarCliente,
    },
    {
      path: '/boletos',
      component: ListarBoleto,
    },
  ],
})

export default router

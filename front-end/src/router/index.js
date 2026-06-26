import { createRouter, createWebHistory } from 'vue-router'

import Home from '../views/home.vue'
import LoginPage from '../views/LoginPage.vue'
import RegisterPage from '../views/RegisterPage.vue'
import DashboardLayout from '../views/DashboardLayout.vue'
import DashBoard from '../views/DashBoard.vue'
import Categorias from '../views/Categorias.vue'
import Pratos from '../views/Pratos.vue'
import Personalizar from '../views/PersonalizacaoPage.vue'
import DadosRestaurante from '../views/DadosRestaurante.vue'
import QrCode from '../views/QrCode.vue'
import NovaCategoria from '../views/NovaCategoria.vue'
import Preview from '../views/Preview.vue'
import EditarCategoria from '../views/EditarCategoria.vue'
import NovoPrato from '../views/NovoPrato.vue'
import EditarPrato from '../views/EditarPrato.vue'
import RecuperarSenha from '../views/RecuperarSenha.vue'
import RedefinirSenha from '../views/RedefinirSenha.vue'
import EmailEnviado from '../views/EmailEnviado.vue'
import Erro404 from '../views/Erro404.vue'
import CardapioCliente from '../views/CardapioCliente.vue'

const routes = [
  {
    path: '/',
    component: Home
  },
  {
    path: '/login',
    component: LoginPage,
    meta: { guestOnly: true } 
  },
  {
    path: '/cadastro',
    component: RegisterPage,
    meta: { guestOnly: true }
  },
  {
    path: '/recuperar-senha',
    component: RecuperarSenha,
    meta: { guestOnly: true }
  },
  {
    path: '/email-enviado',
    component: EmailEnviado,
    meta: { guestOnly: true }
  },
  {
    path: '/redefinir-senha',
    component: RedefinirSenha,
    meta: { guestOnly: true }
  },
  {
    path: '/cardapio-cliente',
    name: 'CardapioCliente',
    component: CardapioCliente
    // Rota totalmente livre: o cliente do restaurante não precisa de login para ver o cardápio
  },
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: { requiresAuth: true }, 
    children: [
      {
        path: '',
        component: DashBoard
      },
      {
        path: 'categorias',
        component: Categorias
      },
      {
        path: 'categorias/nova',
        component: NovaCategoria
      },
      {
        path: 'categorias/editar',
        component: EditarCategoria
      },
      {
        path: 'pratos',
        component: Pratos
      },
      {
        path: 'pratos/novo',
        component: NovoPrato
      },
      {
        path: 'pratos/editar/:id',
        component: EditarPrato
      },
      {
        path: 'personalizacao',
        component: Personalizar
      },
      {
        path: 'dados-restaurante',
        component: DadosRestaurante
      },
      {
        path: 'qrcode',
        component: QrCode
      },
      {
        path: 'preview',
        component: Preview
      }
    ]
  },
  // O 404 SEMPRE na última linha: se a URL digitada não bateu com nada acima, cai aqui.
  {
    path: '/:pathMatch(.*)*',
    name: 'Erro404',
    component: Erro404
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Proteção de rotas (Navigation Guard)
router.beforeEach((to, from, next) => {
  const estaLogado = !!localStorage.getItem('id_restaurante');

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!estaLogado) {
      next('/login');
    } else {
      next();
    }
  } 
  else if (to.matched.some(record => record.meta.guestOnly)) {
    if (estaLogado) {
      next('/dashboard');
    } else {
      next();
    }
  } 
  else {
    next();
  }
})

export default router
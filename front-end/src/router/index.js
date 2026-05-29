import { createRouter, createWebHistory } from 'vue-router'

import Home from '../views/home.vue'
import LoginPage from '../views/LoginPage.vue'
import RegisterPage from '../views/RegisterPage.vue'

const routes = [
  {
    path:'/',
    component: Home
  },
  {
    path:'/login',
    component: LoginPage
  },
  {
    path:'/cadastro',
    component: RegisterPage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
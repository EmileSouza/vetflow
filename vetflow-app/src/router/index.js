import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../pages/Dashboard.vue'
import Patients from '../pages/Patients.vue'
import Services from '../pages/Services.vue'
import Settings from '../pages/Settings.vue'
import Login from '../pages/Login.vue'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
  },
  {
    path: '/patients',
    name: 'Patients',
    component: Patients,
  },
  {
    path: '/services',
    name: 'Services',
    component: Services,
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
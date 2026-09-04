import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import TutorListView from '../views/tutors/TutorListView.vue'
import TutorFormView from '../views/tutors/TutorFormView.vue'
import PetListView from '../views/pets/PetListView.vue'
import PetFormView from '../views/pets/PetFormView.vue'
import PetDetailView from '../views/pets/PetDetailView.vue'
import AgendaView from '../views/agenda/AgendaView.vue'
import AppointmentFormView from '../views/agenda/AppointmentFormView.vue'
import ProntuarioView from '../views/prontuarios/ProntuarioView.vue'
import RegisterView from '../views/RegisterView.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { guest: true }
  },
   {
    path: '/cadastro',
    name: 'Register',
    component: RegisterView,
    meta: { guest: true }
  },
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: { requiresAuth: true }
  },
  {
    path: '/tutores',
    name: 'Tutores',
    component: TutorListView,
    meta: { requiresAuth: true }
  },
  {
    path: '/tutores/novo',
    name: 'TutorCreate',
    component: TutorFormView,
    meta: { requiresAuth: true }
  },
  {
    path: '/pets',
    name: 'Pets',
    component: PetListView,
    meta: { requiresAuth: true }
  },
  {
    path: '/pets/novo',
    name: 'PetCreate',
    component: PetFormView,
    meta: { requiresAuth: true }
  },
  {
    path: '/pets/:id',
    name: 'PetDetail',
    component: PetDetailView,
    meta: { requiresAuth: true }
  },
  {
    path: '/agenda',
    name: 'Agenda',
    component: AgendaView,
    meta: { requiresAuth: true }
  },
  {
    path: '/agenda/novo',
    name: 'AppointmentCreate',
    component: AppointmentFormView,
    meta: { requiresAuth: true }
  },
  {
    path: '/prontuarios',
    name: 'Prontuario',
    component: ProntuarioView,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    next({ name: 'Login' })
    return
  }

  if (to.meta.guest && auth.isLoggedIn) {
    next({ name: 'Dashboard' })
    return
  }

  next()
})

export default router

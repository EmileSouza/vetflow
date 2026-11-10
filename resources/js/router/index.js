import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Autenticação dos veterinários
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'

// Telas públicas
import LandingView from '../views/LandingView.vue'
import BuscarAtendimentoView from '../views/BuscarAtendimentoView.vue'

// Dashboard dos veterinários
import DashboardView from '../views/DashboardView.vue'

// Tutores
import TutorListView from '../views/tutors/TutorListView.vue'
import TutorFormView from '../views/tutors/TutorFormView.vue'

// Pets
import PetListView from '../views/pets/PetListView.vue'
import PetFormView from '../views/pets/PetFormView.vue'
import PetDetailView from '../views/pets/PetDetailView.vue'

// Agenda
import AgendaView from '../views/agenda/AgendaView.vue'
import AppointmentFormView from '../views/agenda/AppointmentFormView.vue'

// Prontuários
import ProntuarioView from '../views/prontuarios/ProntuarioView.vue'

// Administração
import AdminLoginView from '../views/AdminLoginView.vue'
import AdminDashboardView from '../views/AdminDashboardView.vue'
import AdminVeterinariosView from '../views/AdminVeterinariosView.vue'

const routes = [
  // LOGIN DO VETERINÁRIO
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { guest: true }
  },

  // LOGIN ADMINISTRATIVO
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLoginView
  },

  // DASHBOARD ADMINISTRATIVO
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboardView
  },

  // NOVA TELA DE GESTÃO
  {
    path: '/admin/gestao-veterinarios',
    name: 'AdminVeterinarios',
    component: AdminVeterinariosView
  },

  // CADASTRO
  {
    path: '/cadastro',
    name: 'Register',
    component: RegisterView,
    meta: { guest: true }
  },

  // LANDING PAGE
  {
    path: '/',
    name: 'Landing',
    component: LandingView
  },

  // BUSCA PÚBLICA
  {
    path: '/buscar-atendimento',
    name: 'BuscarAtendimento',
    component: BuscarAtendimentoView
  },

  // DASHBOARD DO VETERINÁRIO
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: { requiresAuth: true }
  },

  // TUTORES
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

  // PETS
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

  // AGENDA
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

  // PRONTUÁRIOS
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

// Proteção das rotas dos veterinários.
//
// A administração utiliza outra autenticação,
// baseada em sessão Laravel.
//
// O acesso administrativo é validado em
// /admin/me e nas rotas protegidas do backend.
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    next({
      name: 'Login'
    })
    return
  }

  if (to.meta.guest && auth.isLoggedIn) {
    next({
      name: 'Dashboard'
    })
    return
  }

  next()
})

export default router

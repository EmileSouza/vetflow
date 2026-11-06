import axios from 'axios'

// Cliente HTTP exclusivo da Central Administrativa
const adminApi = axios.create({
  baseURL: '/',

  // Mantém os cookies de sessão nas requisições
  withCredentials: true,

  // Configuração de proteção CSRF do Laravel
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',

  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

// Realizar login administrativo
export function loginAdmin(email, password) {
  return adminApi.post('/admin/session', {
    email,
    password
  })
}

// Consultar a conta administrativa autenticada
export function obterAdmin() {
  return adminApi.get('/admin/me')
}

// Encerrar a sessão administrativa
export function logoutAdmin() {
  return adminApi.delete('/admin/session')
}

export default adminApi

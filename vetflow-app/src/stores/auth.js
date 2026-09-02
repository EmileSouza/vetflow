import { ref } from 'vue'

const token = ref(localStorage.getItem('token') || null)

const isAuthenticated = ref(!!token.value)

const login = (newToken) => {
  token.value = newToken
  localStorage.setItem('token', newToken)
  isAuthenticated.value = true
}

const logout = () => {
  token.value = null
  localStorage.removeItem('token')
  isAuthenticated.value = false
}

export default {
  token,
  isAuthenticated,
  login,
  logout
}
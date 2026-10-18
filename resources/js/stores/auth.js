import { defineStore } from 'pinia'

const defaultUser = {
  nome: 'Dr. VetFlow',
  role: 'Veterinário'
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('vetflow_user') || 'null') || defaultUser,
    token: localStorage.getItem('vetflow_token') || ''
  }),
  getters: {
    isLoggedIn: (state) => !!state.token
  },
  actions: {
    login(token, user = defaultUser) {
      this.token = token
      this.user = user

      localStorage.setItem('vetflow_token', token)
      localStorage.setItem('vetflow_user', JSON.stringify(user))
    },
    logout() {
      this.token = ''
      this.user = defaultUser

      localStorage.removeItem('vetflow_token')
      localStorage.removeItem('vetflow_user')
    }
  }
})
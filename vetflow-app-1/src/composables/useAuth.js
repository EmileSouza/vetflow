import { ref } from 'vue'

const useAuth = () => {
  const user = ref(null)
  const error = ref(null)

  const login = async (email, password) => {
    error.value = null
    try {
      const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        body: JSON.stringify({ email, password })
      })

      const data = await response.json()

      if (response.ok) {
        user.value = data.user
        localStorage.setItem('token', data.token)
      } else {
        error.value = data.message || 'Login failed'
      }
    } catch (err) {
      error.value = 'Error connecting to the server'
    }
  }

  const logout = () => {
    user.value = null
    localStorage.removeItem('token')
  }

  return {
    user,
    error,
    login,
    logout
  }
}

export default useAuth
<template>
  <div class="min-h-screen bg-[#F9FAFB] flex items-center justify-center p-6">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <div class="flex items-center justify-center mb-6">
          <div
            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#10B981] to-[#3B82F6] flex items-center justify-center shadow-lg shadow-emerald-500/20"
          >
            <svg
              class="w-8 h-8 text-white"
              viewBox="0 0 64 64"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
              aria-label="Logo VetFlow"
            >
              <path
                d="M20 18c-4.418 0-8 3.582-8 8 0 2.447 1.111 4.624 2.85 6.074L16 42h14l-4.4-9.926A7.954 7.954 0 0 0 28 26c0-4.418-3.582-8-8-8Zm24 0c-4.418 0-8 3.582-8 8 0 2.447 1.111 4.624 2.85 6.074L40 42h14l-4.4-9.926A7.954 7.954 0 0 0 52 26c0-4.418-3.582-8-8-8ZM32 8c-7.732 0-14 6.268-14 14v4h28v-4c0-7.732-6.268-14-14-14Zm-10 28h20v6H22v-6Zm-7 10h34v4H15v-4Z"
                fill="currentColor"
              />
            </svg>
          </div>
        </div>

        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-slate-900">VetFlow</h1>
          <p class="mt-2 text-sm text-slate-600">
            Gestão que cuida. Tecnologia que conecta.
          </p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
              E-mail
            </label>

            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              autocomplete="email"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#10B981] focus:border-transparent transition"
              placeholder="seu@email.com"
              data-cy="email-input"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
              Senha
            </label>

            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                autocomplete="current-password"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#10B981] focus:border-transparent transition pr-12"
                placeholder="••••••••"
                data-cy="password-input"
              />

              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 transition"
                aria-label="Mostrar ou ocultar senha"
                data-cy="toggle-password"
              >
                <svg
                  v-if="!showPassword"
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  stroke-width="2"
                >
                  <path
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>

                <svg
                  v-else
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  stroke-width="2"
                >
                  <path
                    d="M3 3l18 18M10.58 10.58A2 2 0 0013.41 13.41M9.88 9.88L5.61 5.61A9.95 9.95 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.017 10.017 0 01-3.672 5.038M9.88 9.88l4.24 4.24M3 3l18 18"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </button>
            </div>
          </div>

          <div class="flex justify-end">
            <a href="#" class="text-sm text-[#3B82F6] hover:text-[#2563EB] transition">
              Esqueceu a senha?
            </a>
          </div>

          <button
            type="submit"
            class="w-full bg-[#10B981] text-white font-semibold py-3 rounded-lg hover:bg-[#059669] transition shadow-lg shadow-emerald-500/20"
            data-cy="login-button"
          >
            Entrar
          </button>
        </form>

        <p class="text-center text-sm text-slate-600 mt-6">
          Não tem uma conta?
          <a href="#" class="text-[#3B82F6] font-medium hover:text-[#2563EB] transition">
            Cadastre-se
          </a>
        </p>

        <div
          v-if="error"
          class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg"
        >
          <p class="text-red-600 text-sm flex items-center gap-2">
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              stroke-width="2"
            >
              <path
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            {{ error }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  email: '',
  password: ''
})

const showPassword = ref(false)
const error = ref('')

const handleLogin = async () => {
  error.value = ''

  try {
    const response = await fetch('/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({
        email: form.value.email,
        password: form.value.password
      })
    })

    const data = await response.json()

    if (response.ok) {
      localStorage.setItem('token', data.token)
      alert('Login realizado com sucesso! 🎉')
    } else {
      error.value = data.message || 'Credenciais inválidas'
    }
  } catch (err) {
    error.value = 'Erro ao conectar com o servidor'
  }
}
</script>

<style scoped>
/* Adicione estilos específicos para a página de login aqui, se necessário */
</style>
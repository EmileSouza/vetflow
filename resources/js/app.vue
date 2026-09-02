<template>
  <div class="min-h-screen bg-[#F9FAFB] text-slate-800 antialiased">
    <div class="mx-auto flex min-h-screen max-w-5xl items-center justify-center p-6">
      <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-6 flex justify-center">
          <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#10B981] text-white shadow-sm">
            <svg
              class="h-8 w-8"
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

        <div class="mb-8 text-center">
          <h1 class="text-3xl font-bold tracking-tight text-slate-900">VetFlow</h1>
          <p class="mt-2 text-sm text-slate-600">
            Gestão que cuida. Tecnologia que conecta.
          </p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5" novalidate>
          <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
              E-mail
            </label>

            <input
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              required
              placeholder="seu@email.com"
              class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#10B981]"
              data-cy="email-input"
            />
          </div>

          <div>
            <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
              Senha
            </label>

            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                placeholder="••••••••"
                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 pr-12 text-slate-900 placeholder:text-slate-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#10B981]"
                data-cy="password-input"
              />

              <button
                type="button"
                @click.prevent="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 transition hover:text-slate-700"
                aria-label="Mostrar ou ocultar senha"
                data-cy="toggle-password"
              >
                <svg
                  v-if="!showPassword"
                  class="h-5 w-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  stroke-width="2"
                  aria-hidden="true"
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
                  class="h-5 w-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  stroke-width="2"
                  aria-hidden="true"
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
            <a href="#" class="text-sm text-[#3B82F6] transition hover:text-[#2563EB]">
              Esqueceu a senha?
            </a>
          </div>

          <button
            type="submit"
            class="w-full rounded-lg bg-[#10B981] py-3 font-semibold text-white transition hover:bg-[#059669]"
            data-cy="login-button"
          >
            Entrar
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
          Não tem uma conta?
          <a href="#" class="font-medium text-[#3B82F6] transition hover:text-[#2563EB]">
            Cadastre-se
          </a>
        </p>

        <div
          v-if="status.message"
          :class="status.type === 'success'
            ? 'mt-6 border border-emerald-200 bg-emerald-50 text-emerald-700'
            : 'mt-6 border border-red-200 bg-red-50 text-red-700'"
          class="rounded-lg p-3 text-sm"
          aria-live="polite"
        >
          {{ status.message }}
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
const status = ref({
  type: '',
  message: ''
})

const handleLogin = async () => {
  status.value = { type: '', message: '' }

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
      localStorage.setItem('token', data.token || '')
      status.value = {
        type: 'success',
        message: 'Login realizado com sucesso.'
      }

      window.location.href = '/dashboard'
    } else {
      status.value = {
        type: 'error',
        message: data.message || 'Credenciais inválidas.'
      }
    }
  } catch (error) {
    status.value = {
      type: 'error',
      message: 'Não foi possível conectar ao servidor.'
    }
  }
}
</script>

<template>
  <router-view />
</template>

<script setup>
</script>
<template>
  <div class="min-h-screen bg-slate-100">
    <div class="mx-auto grid min-h-screen max-w-6xl grid-cols-1 overflow-hidden lg:grid-cols-2">
      <div class="relative hidden items-center justify-center bg-gradient-to-br from-emerald-500 via-teal-600 to-sky-700 p-10 text-white lg:flex">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.2),transparent_35%)]"></div>

        <div class="relative max-w-md">
          <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
            <svg viewBox="0 0 64 64" class="h-10 w-10 text-white" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 18c-4.418 0-8 3.582-8 8 0 2.447 1.111 4.624 2.85 6.074L16 42h14l-4.4-9.926A7.954 7.954 0 0 0 28 26c0-4.418-3.582-8-8-8Zm24 0c-4.418 0-8 3.582-8 8 0 2.447 1.111 4.624 2.85 6.074L40 42h14l-4.4-9.926A7.954 7.954 0 0 0 52 26c0-4.418-3.582-8-8-8ZM32 8c-7.732 0-14 6.268-14 14v4h28v-4c0-7.732-6.268-14-14-14Zm-10 28h20v6H22v-6Zm-7 10h34v4H15v-4Z" fill="currentColor"/>
            </svg>
          </div>

          <h1 class="text-4xl font-bold leading-tight">VetFlow</h1>
          <p class="mt-4 text-lg text-emerald-50/90">
            Gestão moderna para clínicas veterinárias, com controle de tutores, pets, consulta e agenda.
          </p>

          <div class="mt-8 grid gap-4 text-sm text-emerald-50/85">
            <div class="flex items-center gap-3">
              <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/10">✓</span>
              Agenda centralizada
            </div>
            <div class="flex items-center gap-3">
              <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/10">✓</span>
              Prontuários clínicos
            </div>
            <div class="flex items-center gap-3">
              <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/10">✓</span>
              Fluxo de atendimento ágil
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60">
          <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
              <svg viewBox="0 0 64 64" class="h-8 w-8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 18c-4.418 0-8 3.582-8 8 0 2.447 1.111 4.624 2.85 6.074L16 42h14l-4.4-9.926A7.954 7.954 0 0 0 28 26c0-4.418-3.582-8-8-8Zm24 0c-4.418 0-8 3.582-8 8 0 2.447 1.111 4.624 2.85 6.074L40 42h14l-4.4-9.926A7.954 7.954 0 0 0 52 26c0-4.418-3.582-8-8-8ZM32 8c-7.732 0-14 6.268-14 14v4h28v-4c0-7.732-6.268-14-14-14Zm-10 28h20v6H22v-6Zm-7 10h34v4H15v-4Z" fill="currentColor"/>
              </svg>
            </div>

            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Entrar</h2>
            <p class="mt-2 text-sm text-slate-500">
              Acesse sua conta e continue o atendimento.
            </p>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5" novalidate>
            <div>
              <label for="email" class="mb-2 block text-sm font-medium text-slate-700">E-mail</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                placeholder="seu@email.com"
              />
            </div>

            <div>
              <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Senha</label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                  placeholder="••••••••"
                />

                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700"
                  aria-label="Mostrar senha"
                >
                  <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M3 3l18 18M10.58 10.58A2 2 0 0013.41 13.41M9.88 9.88L5.61 5.61A9.95 9.95 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.017 10.017 0 01-3.672 5.038M9.88 9.88l4.24 4.24M3 3l18 18" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="flex items-center justify-between text-sm">
              <label class="inline-flex items-center gap-2 text-slate-600">
                <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                Lembrar-me
              </label>

              <a href="#" class="font-medium text-emerald-600 hover:text-emerald-700">
                Esqueceu a senha?
              </a>
            </div>

            <button
              type="submit"
              class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
            >
              Entrar
            </button>
          </form>

          <div class="mt-6 text-center text-sm text-slate-600">
            Não tem conta?
            <a href="#" class="font-semibold text-emerald-600 hover:text-emerald-700">Solicitar acesso</a>
          </div>

          <div
            v-if="status.message"
            :class="status.type === 'success'
              ? 'mt-6 border border-emerald-200 bg-emerald-50 text-emerald-700'
              : 'mt-6 border border-red-200 bg-red-50 text-red-700'"
            class="rounded-xl p-3 text-sm"
            aria-live="polite"
          >
            {{ status.message }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { authService } from '../services/authService'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  email: '',
  password: ''
})

const showPassword = ref(false)
const status = ref({ type: '', message: '' })

const handleLogin = async () => {
  status.value = { type: '', message: '' }

  if (!form.email || !form.password) {
    status.value = {
      type: 'error',
      message: 'Informe e-mail e senha para continuar.'
    }
    return
  }

  try {
    const { data } = await authService.login({
      email: form.email,
      password: form.password
    })

    auth.login(data.token || 'demo-token', data.user || {
      name: 'Dr. VetFlow',
      role: 'Veterinário'
    })

    router.replace('/dashboard')
  } catch (error) {
    status.value = {
      type: 'error',
      message: error.response?.data?.message || 'Credenciais inválidas.'
    }
  }
}
</script>
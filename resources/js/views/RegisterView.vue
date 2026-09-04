<template>
  <div class="min-h-screen bg-slate-100">
    <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center p-6">
      <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60">

        <!-- Identidade -->
        <div class="mb-8 text-center">
          <div
            class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700"
          >
            <svg
              viewBox="0 0 64 64"
              class="h-8 w-8"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M20 18c-4.418 0-8 3.582-8 8 0 2.447 1.111 4.624 2.85 6.074L16 42h14l-4.4-9.926A7.954 7.954 0 0 0 28 26c0-4.418-3.582-8-8-8Zm24 0c-4.418 0-8 3.582-8 8 0 2.447 1.111 4.624 2.85 6.074L40 42h14l-4.4-9.926A7.954 7.954 0 0 0 52 26c0-4.418-3.582-8-8-8ZM32 8c-7.732 0-14 6.268-14 14v4h28v-4c0-7.732-6.268-14-14-14Zm-10 28h20v6H22v-6Zm-7 10h34v4H15v-4Z"
                fill="currentColor"
              />
            </svg>
          </div>

          <h1 class="text-3xl font-bold tracking-tight text-slate-900">
            Criar sua conta
          </h1>

          <p class="mt-2 text-sm text-slate-500">
            Cadastre-se para começar a usar o VetFlow
          </p>
        </div>

        <!-- Formulário -->
        <form @submit.prevent="handleRegister" class="space-y-4">

          <!-- Nome -->
          <div>
            <label
              for="nome"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Nome
            </label>

            <input
              id="nome"
              v-model="form.nome"
              type="text"
              autocomplete="name"
              placeholder="Seu nome completo"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
            />
          </div>

          <!-- CRMV -->
          <div>
            <label
              for="crmv"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              CRMV
            </label>

            <input
              id="crmv"
              v-model="form.crmv"
              type="text"
              placeholder="Ex.: 12345"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
            />
          </div>

          <!-- E-mail -->
          <div>
            <label
              for="email"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              E-mail
            </label>

            <input
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              placeholder="seu@email.com"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
            />
          </div>

          <!-- Telefone -->
          <div>
            <label
              for="telefone"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Telefone
            </label>

            <input
              id="telefone"
              v-model="form.telefone"
              type="tel"
              placeholder="(00) 00000-0000"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
            />
          </div>

          <!-- Especialidade -->
          <div>
            <label
              for="especialidade"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Especialidade
            </label>

            <input
              id="especialidade"
              v-model="form.especialidade"
              type="text"
              placeholder="Ex.: Clínica de pequenos animais"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
            />
          </div>

          <!-- Senha -->
          <div>
            <label
              for="password"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Senha
            </label>

            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="new-password"
                placeholder="Mínimo de 8 caracteres"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
              />

              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700"
                aria-label="Mostrar senha"
              >
                👁
              </button>
            </div>
          </div>

          <!-- Confirmar senha -->
          <div>
            <label
              for="password_confirmation"
              class="mb-2 block text-sm font-medium text-slate-700"
            >
              Confirmar senha
            </label>

            <div class="relative">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                autocomplete="new-password"
                placeholder="Digite a senha novamente"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
              />

              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700"
                aria-label="Mostrar confirmação da senha"
              >
                👁
              </button>
            </div>
          </div>

          <!-- Botão -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-base font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ loading ? 'Criando conta...' : 'Criar conta' }}
          </button>
        </form>

        <!-- Mensagem -->
        <div
          v-if="status.message"
          :class="
            status.type === 'success'
              ? 'mt-5 border border-emerald-200 bg-emerald-50 text-emerald-700'
              : 'mt-5 border border-red-200 bg-red-50 text-red-700'
          "
          class="rounded-xl p-3 text-sm"
          aria-live="polite"
        >
          {{ status.message }}
        </div>

        <!-- Voltar -->
        <div class="mt-6 text-center text-sm text-slate-600">
          Já possui uma conta?

          <button
            type="button"
            @click="goToLogin"
            class="font-semibold text-emerald-600 hover:text-emerald-700"
          >
            Entrar
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from '../services/authService'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  nome: '',
  email: '',
  password: '',
  password_confirmation: '',
  crmv: '',
  telefone: '',
  especialidade: ''
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const loading = ref(false)

const status = ref({
  type: '',
  message: ''
})

const handleRegister = async () => {
  status.value = {
    type: '',
    message: ''
  }

  if (
    !form.nome ||
    !form.email ||
    !form.password ||
    !form.password_confirmation ||
    !form.crmv
  ) {
    status.value = {
      type: 'error',
      message: 'Preencha todos os campos obrigatórios.'
    }

    return
  }

  if (form.password !== form.password_confirmation) {
    status.value = {
      type: 'error',
      message: 'As senhas não são iguais.'
    }

    return
  }

  loading.value = true

  try {
    const { data } = await authService.register(form)

    auth.login(data.token, data.veterinario)

    router.replace('/dashboard')
  } catch (error) {
    const errors = error.response?.data?.errors

    if (errors) {
      const firstError = Object.values(errors)[0]?.[0]

      status.value = {
        type: 'error',
        message: firstError || 'Não foi possível realizar o cadastro.'
      }
    } else {
      status.value = {
        type: 'error',
        message:
          error.response?.data?.message ||
          'Não foi possível realizar o cadastro.'
      }
    }
  } finally {
    loading.value = false
  }
}

const goToLogin = () => {
  router.push('/login')
}
</script>
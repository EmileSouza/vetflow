<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
      <h1 class="text-3xl font-bold text-slate-800 mb-2">VetFlow</h1>
      <p class="text-slate-500 mb-6">Faça login para continuar</p>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">E-mail</label>
          <input v-model="form.email" type="email" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="seu@email.com" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Senha</label>
          <input v-model="form.password" type="password" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="••••••••" />
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 transition">
          Entrar
        </button>
      </form>

      <p v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const error = ref('')

const form = reactive({
  email: '',
  password: '',
})

const handleLogin = () => {
  if (!form.email || !form.password) {
    error.value = 'Preencha e-mail e senha.'
    return
  }

  error.value = ''
  router.push('/dashboard')
}
</script>

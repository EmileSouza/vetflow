<template>
  <div class="settings-page">
    <h1 class="text-2xl font-bold mb-4">Configurações</h1>
    <p class="text-gray-600 mb-6">Ajuste suas preferências e configurações do sistema abaixo.</p>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700">Nome de Usuário</label>
        <input
          id="username"
          v-model="settings.username"
          type="text"
          required
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-500"
        />
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
        <input
          id="email"
          v-model="settings.email"
          type="email"
          required
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-500"
        />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
        <input
          id="password"
          v-model="settings.password"
          type="password"
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-500"
        />
      </div>

      <button
        type="submit"
        class="w-full bg-green-500 text-white font-semibold py-2 rounded-md hover:bg-green-600 transition"
      >
        Salvar Configurações
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const settings = ref({
  username: '',
  email: '',
  password: ''
})

const handleSubmit = () => {
  // Lógica para salvar as configurações
  console.log('Configurações salvas:', settings.value)
}
</script>

<style scoped>
.settings-page {
  padding: 20px;
}
</style>
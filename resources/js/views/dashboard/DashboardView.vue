<template>
  <div class="min-h-screen bg-slate-100 p-8">
    <div class="max-w-5xl mx-auto">
      <header class="mb-8 flex items-center justify-between">
        <div>
          <p class="text-sm uppercase tracking-wide text-slate-500">Dashboard</p>
          <h1 class="text-3xl font-bold text-slate-800">VetFlow</h1>
        </div>

        <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
          Sair
        </button>
      </header>

      <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow p-6">
          <p class="text-slate-500">Tutores</p>
          <h2 class="text-3xl font-bold mt-2">128</h2>
        </div>
        <div class="bg-white rounded-2xl shadow p-6">
          <p class="text-slate-500">Pets</p>
          <h2 class="text-3xl font-bold mt-2">240</h2>
        </div>
        <div class="bg-white rounded-2xl shadow p-6">
          <p class="text-slate-500">Agendamentos</p>
          <h2 class="text-3xl font-bold mt-2">36</h2>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const logout = () => {
  router.push('/')
}
</script>

<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Dashboard" actionLabel="+ Novo tutor" />

      <div class="p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold text-slate-900">
            Olá, {{ user.name }} 👋
          </h2>
        </div>

        <div class="mb-6 grid gap-4 md:grid-cols-3">
          <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Pets cadastrados</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">128</p>
          </div>

          <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Agendamentos</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">18</p>
          </div>

          <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Atendimentos hoje</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">09</p>
          </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">Próximos atendimentos</h3>
            <button class="text-sm font-medium text-[#3B82F6]">Ver todos</button>
          </div>

          <div class="space-y-3">
            <div
              v-for="appointment in appointments"
              :key="appointment.time"
              class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3"
            >
              <div class="flex items-center gap-4">
                <span class="w-16 text-sm font-semibold text-slate-600">{{ appointment.time }}</span>
                <div>
                  <div class="font-medium text-slate-900">{{ appointment.pet }}</div>
                  <div class="text-sm text-slate-500">{{ appointment.owner }}</div>
                </div>
              </div>

              <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                {{ appointment.type }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import AppSidebar from '../components/layout/AppSidebar.vue'
import AppHeader from '../components/layout/AppHeader.vue'

const auth = useAuthStore()
const user = computed(() => auth.user)

const appointments = [
  { time: '09:00', pet: 'Mel', owner: 'Maria Silva', type: 'Consulta' },
  { time: '10:30', pet: 'Nina', owner: 'João Santos', type: 'Retorno' },
  { time: '14:00', pet: 'Thor', owner: 'Ana Oliveira', type: 'Vacinação' }
]
</script>
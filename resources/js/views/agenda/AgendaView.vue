<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Agenda" actionLabel="+ Novo agendamento" actionRoute="/agenda/novo" />

      <div class="p-6">
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-500">Hoje</p>
              <h2 class="mt-1 text-2xl font-semibold text-slate-900">31 de agosto</h2>
            </div>

            <button
              type="button"
              @click="goToNewAppointment"
              class="rounded-lg bg-[#10B981] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#059669]"
            >
              + Novo agendamento
            </button>
          </div>
        </div>

        <div class="space-y-4">
          <article
            v-for="appointment in appointments"
            :key="appointment.id"
            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
          >
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
              <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-sm font-semibold text-slate-700">
                  {{ appointment.time.split(':')[0] }}
                </div>

                <div>
                  <div class="text-lg font-semibold text-slate-900">{{ appointment.time }}</div>
                  <div class="mt-1 text-base font-medium text-slate-800">{{ appointment.pet }}</div>
                  <div class="text-sm text-slate-500">{{ appointment.owner }}</div>
                </div>
              </div>

              <div class="flex items-center gap-3">
                <span
                  :class="appointment.type === 'Consulta'
                    ? 'bg-emerald-100 text-emerald-700'
                    : appointment.type === 'Retorno'
                      ? 'bg-blue-100 text-blue-700'
                      : 'bg-amber-100 text-amber-700'"
                  class="rounded-full px-2.5 py-1 text-xs font-medium"
                >
                  {{ appointment.type }}
                </span>

                <button
                  type="button"
                  class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                  Ver
                </button>
              </div>
            </div>
          </article>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import AppSidebar from '../../components/layout/AppSidebar.vue'
import AppHeader from '../../components/layout/AppHeader.vue'

const router = useRouter()

const appointments = [
  { id: 1, time: '09:00', pet: 'Mel', owner: 'Maria Silva', type: 'Consulta' },
  { id: 2, time: '10:30', pet: 'Nina', owner: 'João Santos', type: 'Retorno' },
  { id: 3, time: '14:00', pet: 'Thor', owner: 'Ana Oliveira', type: 'Vacinação' }
]

const goToNewAppointment = () => {
  router.push('/agenda/novo')
}
</script>
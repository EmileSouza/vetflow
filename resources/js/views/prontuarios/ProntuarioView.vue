<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Prontuário" actionLabel="+ Nova consulta" />

      <div class="p-6">
        <div class="mx-auto max-w-5xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="mb-6 flex items-center justify-between">
            <div>
              <p class="text-sm uppercase tracking-wide text-slate-500">Pet</p>
              <h2 class="mt-1 text-3xl font-bold text-slate-900">{{ pet.name }}</h2>
            </div>

            <button
              type="button"
              @click="goToPetList"
              class="rounded-lg bg-[#10B981] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#059669]"
            >
              + Nova consulta
            </button>
          </div>

          <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <p class="text-xs uppercase tracking-wide text-slate-500">Tutor</p>
              <p class="mt-2 text-base font-semibold text-slate-900">{{ pet.owner }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <p class="text-xs uppercase tracking-wide text-slate-500">Última consulta</p>
              <p class="mt-2 text-base font-semibold text-slate-900">{{ pet.lastVisit }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <p class="text-xs uppercase tracking-wide text-slate-500">Motivo</p>
              <p class="mt-2 text-base font-semibold text-slate-900">{{ pet.reason }}</p>
            </div>
          </div>

          <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4">
              <h3 class="text-lg font-semibold text-slate-900">Observações</h3>
              <p class="mt-3 text-sm leading-6 text-slate-600">
                {{ pet.observations }}
              </p>
            </div>

            <div class="space-y-4">
              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Peso</p>
                <p class="mt-2 text-base font-semibold text-slate-900">{{ pet.weight }}</p>
              </div>

              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Temperatura</p>
                <p class="mt-2 text-base font-semibold text-slate-900">{{ pet.temperature }}</p>
              </div>

              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Prescrição</p>
                <p class="mt-2 text-base font-semibold text-slate-900">{{ pet.prescription }}</p>
              </div>
            </div>
          </div>
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

const pet = {
  name: 'Mel',
  owner: 'Maria Silva',
  lastVisit: '18/08/2026',
  reason: 'Consulta de rotina',
  observations:
    'Animal tranquila, apetite normal, comportamento ativo. Sem alterações relevantes no exame clínico. Encaminhado para revisão em 30 dias.',
  weight: '5,4 kg',
  temperature: '38,6°C',
  prescription: 'Antipulgas mensal · reforço de hidratação'
}

const goToPetList = () => {
  router.push('/pets')
}
</script>
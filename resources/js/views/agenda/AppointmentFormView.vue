<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Novo agendamento" />

      <div class="p-6">
        <div class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <form @submit.prevent="saveAppointment" class="space-y-6" novalidate>
            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Pet *
                </label>
                <select
                  v-model="form.pet"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                >
                  <option value="">Selecione o pet</option>
                  <option value="Mel">Mel</option>
                  <option value="Nina">Nina</option>
                  <option value="Thor">Thor</option>
                  <option value="Luna">Luna</option>
                </select>
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Tutor *
                </label>
                <select
                  v-model="form.owner"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                >
                  <option value="">Selecione o tutor</option>
                  <option value="Maria Silva">Maria Silva</option>
                  <option value="João Santos">João Santos</option>
                  <option value="Ana Oliveira">Ana Oliveira</option>
                  <option value="Carlos Mendes">Carlos Mendes</option>
                </select>
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Data *
                </label>
                <input
                  v-model="form.date"
                  type="date"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Hora *
                </label>
                <input
                  v-model="form.time"
                  type="time"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Tipo
                </label>
                <select
                  v-model="form.type"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                >
                  <option value="Consulta">Consulta</option>
                  <option value="Retorno">Retorno</option>
                  <option value="Vacinação">Vacinação</option>
                  <option value="Exame">Exame</option>
                </select>
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Status
                </label>
                <select
                  v-model="form.status"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                >
                  <option value="Agendado">Agendado</option>
                  <option value="Confirmado">Confirmado</option>
                  <option value="Em andamento">Em andamento</option>
                  <option value="Concluído">Concluído</option>
                </select>
              </div>

              <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Observação
                </label>
                <textarea
                  v-model="form.note"
                  rows="4"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  placeholder="Descreva a necessidade do atendimento"
                />
              </div>
            </div>

            <div
              v-if="status.message"
              :class="
                status.type === 'success'
                  ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                  : 'border border-red-200 bg-red-50 text-red-700'
              "
              class="rounded-lg p-3 text-sm"
            >
              {{ status.message }}
            </div>

            <div class="flex items-center justify-end gap-3">
              <button
                type="button"
                @click="goBack"
                class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
              >
                Cancelar
              </button>

              <button
                type="submit"
                class="rounded-lg bg-[#10B981] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#059669]"
              >
                Salvar agendamento
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { appointmentService } from '../../services/appointmentService'
import AppSidebar from '../../components/layout/AppSidebar.vue'
import AppHeader from '../../components/layout/AppHeader.vue'

const router = useRouter()

const form = reactive({
  pet: '',
  owner: '',
  date: '',
  time: '',
  type: 'Consulta',
  status: 'Agendado',
  note: ''
})

const status = ref({
  type: '',
  message: ''
})

const saveAppointment = async () => {
  if (!form.pet || !form.owner || !form.date || !form.time) {
    status.value = {
      type: 'error',
      message: 'Pet, tutor, data e hora são obrigatórios.'
    }
    return
  }

  try {
    await appointmentService.create({
      pet: form.pet,
      owner: form.owner,
      date: form.date,
      time: form.time,
      type: form.type,
      status: form.status,
      note: form.note
    })

    status.value = {
      type: 'success',
      message: 'Agendamento salvo com sucesso.'
    }

    setTimeout(() => {
      router.push('/agenda')
    }, 600)
  } catch (error) {
    status.value = {
      type: 'error',
      message: error.response?.data?.message || 'Não foi possível salvar o agendamento.'
    }
  }
}

const goBack = () => {
  router.back()
}
</script>
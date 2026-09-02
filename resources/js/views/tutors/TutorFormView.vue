<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Cadastrar tutor" />

      <div class="p-6">
        <div class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <form @submit.prevent="saveTutor" class="space-y-6" novalidate>
            <div class="grid gap-5 md:grid-cols-2">
              <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Nome completo *
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  placeholder="Digite o nome completo"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Telefone *
                </label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  placeholder="(31) 99999-9999"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  E-mail *
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  placeholder="seu@email.com"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  CPF
                </label>
                <input
                  v-model="form.cpf"
                  type="text"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  placeholder="000.000.000-00"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Cidade
                </label>
                <input
                  v-model="form.city"
                  type="text"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  placeholder="Belo Horizonte"
                />
              </div>

              <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">
                  Endereço
                </label>
                <input
                  v-model="form.address"
                  type="text"
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  placeholder="Rua, número, bairro"
                />
              </div>
            </div>

            <div
              v-if="status.message"
              :class="status.type === 'success'
                ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                : 'border border-red-200 bg-red-50 text-red-700'"
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
                Salvar
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
import AppSidebar from '../../components/layout/AppSidebar.vue'
import AppHeader from '../../components/layout/AppHeader.vue'

const router = useRouter()

const form = reactive({
  name: '',
  phone: '',
  email: '',
  cpf: '',
  city: '',
  address: ''
})

const status = ref({
  type: '',
  message: ''
})

const saveTutor = () => {
  if (!form.name || !form.phone || !form.email) {
    status.value = {
      type: 'error',
      message: 'Nome, telefone e e-mail são obrigatórios.'
    }
    return
  }

  status.value = {
    type: 'success',
    message: 'Tutor cadastrado com sucesso.'
  }

  setTimeout(() => {
    router.push('/tutores')
  }, 600)
}

const goBack = () => {
  router.back()
}
</script>
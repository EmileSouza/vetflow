<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Cadastrar pet" />

      <div class="p-6">
        <div class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <form @submit.prevent="savePet" class="space-y-6" novalidate>
            <div class="grid gap-6 md:grid-cols-[220px_1fr]">
              <div class="flex flex-col items-center">
                <div class="relative">
                  <div
                    class="flex h-52 w-52 items-center justify-center overflow-hidden rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-5xl text-slate-400"
                  >
                    <img
                      v-if="photoPreview"
                      :src="photoPreview"
                      alt="Preview do pet"
                      class="h-full w-full object-cover"
                    />
                    <span v-else>🐾</span>
                  </div>

                  <label
                    class="mt-3 inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                  >
                    <input
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleFileChange"
                    />
                    Enviar foto
                  </label>
                </div>
              </div>

              <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Nome *
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                    placeholder="Digite o nome do pet"
                  />
                </div>

                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Espécie *
                  </label>
                  <select
                    v-model="form.species"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  >
                    <option value="Cachorro">Cachorro</option>
                    <option value="Gato">Gato</option>
                    <option value="Outros">Outros</option>
                  </select>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Raça
                  </label>
                  <input
                    v-model="form.breed"
                    type="text"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                    placeholder="Ex: Labrador"
                  />
                </div>

                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Sexo
                  </label>
                  <select
                    v-model="form.sex"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  >
                    <option value="Macho">Macho</option>
                    <option value="Fêmea">Fêmea</option>
                  </select>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Data de nascimento
                  </label>
                  <input
                    v-model="form.birthDate"
                    type="date"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  />
                </div>

                <div class="md:col-span-2">
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Tutor *
                  </label>
                  <select
                    v-model="form.tutorId"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
                  >
                    <option value="">Selecione o tutor</option>
                    <option v-for="tutor in tutors" :key="tutor.id" :value="tutor.id">
                      {{ tutor.name }}
                    </option>
                  </select>
                </div>
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

const tutors = [
  { id: 1, name: 'Maria Silva' },
  { id: 2, name: 'João Santos' },
  { id: 3, name: 'Ana Oliveira' }
]

const form = reactive({
  name: '',
  species: 'Cachorro',
  breed: '',
  sex: 'Macho',
  birthDate: '',
  tutorId: ''
})

const photoPreview = ref('')
const status = ref({
  type: '',
  message: ''
})

const handleFileChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => {
    photoPreview.value = e.target?.result || ''
  }
  reader.readAsDataURL(file)
}

const savePet = () => {
  if (!form.name || !form.tutorId) {
    status.value = {
      type: 'error',
      message: 'Nome e tutor são obrigatórios.'
    }
    return
  }

  status.value = {
    type: 'success',
    message: 'Pet cadastrado com sucesso.'
  }

  setTimeout(() => {
    router.push('/pets')
  }, 600)
}

const goBack = () => {
  router.back()
}
</script>
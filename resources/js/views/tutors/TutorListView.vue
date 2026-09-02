<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Tutores" actionLabel="+ Novo tutor" actionRoute="/tutores/novo" />

      <div class="p-6">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <div class="relative w-full max-w-md">
            <input
              v-model="search"
              type="text"
              placeholder="Buscar tutor..."
              class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 pl-10 text-sm text-slate-700 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
              data-cy="tutor-search"
            />
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔎</span>
          </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                  Nome
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                  Telefone
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                  E-mail
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                  Pets
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                  Ações
                </th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 bg-white">
              <tr
                v-for="tutor in filteredTutors"
                :key="tutor.id"
                class="transition hover:bg-slate-50"
              >
                <td class="px-4 py-3 text-sm font-medium text-slate-900">
                  {{ tutor.name }}
                </td>
                <td class="px-4 py-3 text-sm text-slate-600">
                  {{ tutor.phone }}
                </td>
                <td class="px-4 py-3 text-sm text-slate-600">
                  {{ tutor.email }}
                </td>
                <td class="px-4 py-3 text-sm text-slate-600">
                  {{ tutor.petsCount }}
                </td>
                <td class="px-4 py-3 text-sm">
                  <div class="flex items-center gap-3">
                    <button
                      type="button"
                      @click="goToEdit"
                      class="font-medium text-[#3B82F6] hover:text-[#2563EB]"
                    >
                      Editar
                    </button>
                    <button
                      type="button"
                      @click="removeTutor(tutor.id)"
                      class="font-medium text-red-500 hover:text-red-600"
                    >
                      Excluir
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="filteredTutors.length === 0">
                <td colspan="5" class="px-4 py-10 text-center">
                  <div class="flex flex-col items-center justify-center gap-3 text-slate-500">
                    <span class="text-4xl">👥</span>
                    <p class="text-base font-medium text-slate-600">Nenhum tutor encontrado.</p>
                    <p class="text-sm">Tente outro termo ou cadastre um novo tutor.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-4 flex items-center justify-between">
          <p class="text-sm text-slate-500">
            Mostrando {{ filteredTutors.length }} de {{ tutors.length }}
          </p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppSidebar from '../../components/layout/AppSidebar.vue'
import AppHeader from '../../components/layout/AppHeader.vue'

const router = useRouter()

const tutors = ref([
  { id: 1, name: 'Maria Silva', phone: '(31) 99876-1234', email: 'maria@email.com', petsCount: 2 },
  { id: 2, name: 'João Santos', phone: '(31) 99123-4567', email: 'joao@email.com', petsCount: 3 },
  { id: 3, name: 'Ana Oliveira', phone: '(31) 98765-4321', email: 'ana@email.com', petsCount: 1 },
  { id: 4, name: 'Carlos Mendes', phone: '(31) 98456-7890', email: 'carlos@email.com', petsCount: 4 }
])

const search = ref('')

const filteredTutors = computed(() => {
  if (!search.value.trim()) return tutors.value

  const term = search.value.toLowerCase()

  return tutors.value.filter((tutor) =>
    tutor.name.toLowerCase().includes(term) ||
    tutor.email.toLowerCase().includes(term) ||
    tutor.phone.toLowerCase().includes(term)
  )
})

const goToEdit = () => {
  router.push('/tutores/novo')
}

const removeTutor = (id) => {
  tutors.value = tutors.value.filter((tutor) => tutor.id !== id)
}
</script>
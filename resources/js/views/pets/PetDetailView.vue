<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Detalhes do pet" />

      <div class="p-6">
        <button
          type="button"
          @click="goBack"
          class="mb-6 text-sm font-medium text-[#3B82F6] hover:text-[#2563EB]"
        >
          ← Voltar para pets
        </button>

        <div
          v-if="pet"
          class="max-w-3xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
          <div class="h-64 overflow-hidden bg-slate-100">
            <img
              :src="pet.image"
              :alt="pet.name"
              class="h-full w-full object-cover"
            />
          </div>

          <div class="p-6">
            <div class="mb-6 flex items-start justify-between gap-4">
              <div>
                <h1 class="text-2xl font-bold text-slate-900">
                  {{ pet.name }}
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                  {{ pet.species }} · {{ pet.breed }}
                </p>
              </div>

              <span
                class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium uppercase tracking-wide text-slate-600"
              >
                {{ pet.species }}
              </span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                  Nome
                </p>
                <p class="mt-1 font-medium text-slate-900">
                  {{ pet.name }}
                </p>
              </div>

              <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                  Espécie
                </p>
                <p class="mt-1 font-medium text-slate-900">
                  {{ pet.species }}
                </p>
              </div>

              <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                  Raça
                </p>
                <p class="mt-1 font-medium text-slate-900">
                  {{ pet.breed }}
                </p>
              </div>

              <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                  Tutor
                </p>
                <p class="mt-1 font-medium text-slate-900">
                  {{ pet.owner }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div
          v-else
          class="rounded-xl border border-red-200 bg-red-50 p-6 text-sm text-red-700"
        >
          Pet não encontrado.
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppSidebar from '../../components/layout/AppSidebar.vue'
import AppHeader from '../../components/layout/AppHeader.vue'

const route = useRoute()
const router = useRouter()

const pets = ref([
  {
    id: 1,
    name: 'Mel',
    species: 'Cachorro',
    breed: 'Shih Tzu',
    owner: 'Maria Silva',
    image: '/images/dog-1.jpg'
  },
  {
    id: 2,
    name: 'Nina',
    species: 'Gato',
    breed: 'Siamês',
    owner: 'João Santos',
    image: '/images/cat-1.jpg'
  },
  {
    id: 3,
    name: 'Thor',
    species: 'Cachorro',
    breed: 'Labrador',
    owner: 'Ana Oliveira',
    image: '/images/dog-2.jpg'
  }
])

const pet = computed(() => {
  const id = Number(route.params.id)

  return pets.value.find((item) => item.id === id)
})

const goBack = () => {
  router.push('/pets')
}
</script>
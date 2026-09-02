<template>
  <div class="flex min-h-screen bg-[#F9FAFB]">
    <AppSidebar />

    <main class="flex-1">
      <AppHeader title="Pets" actionLabel="+ Cadastrar pet" actionRoute="/pets/novo" />

      <div class="p-6">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <div class="relative w-full max-w-md">
            <input
              v-model="search"
              type="text"
              placeholder="Buscar pet..."
              class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 pl-10 text-sm text-slate-700 placeholder:text-slate-400 focus:border-[#10B981] focus:outline-none focus:ring-2 focus:ring-[#10B981]/20"
            />
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔎</span>
          </div>
        </div>

        <div v-if="filteredPets.length" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <article
            v-for="pet in filteredPets"
            :key="pet.id"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md"
          >
            <div class="h-44 overflow-hidden bg-slate-100">
              <img :src="pet.image" :alt="pet.name" class="h-full w-full object-cover" />
            </div>

            <div class="p-4">
              <div class="mb-3 flex items-start justify-between gap-3">
                <div>
                  <h3 class="text-lg font-semibold text-slate-900">{{ pet.name }}</h3>
                  <p class="text-sm text-slate-500">{{ pet.species }} · {{ pet.breed }}</p>
                </div>

                <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-medium uppercase tracking-wide text-slate-600">
                  {{ pet.species }}
                </span>
              </div>

              <div class="mb-4 text-sm text-slate-600">
                Tutor: <span class="font-medium text-slate-800">{{ pet.owner }}</span>
              </div>

              <div class="flex items-center justify-end gap-3">
                <button type="button" @click="goToDetail(pet.id)" class="text-sm font-medium text-[#3B82F6] hover:text-[#2563EB]">
                  Ver
                </button>
                <button type="button" @click="goToDetail(pet.id)" class="text-sm font-medium text-slate-600 hover:text-slate-800">
                  Editar
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
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppSidebar from '../../components/layout/AppSidebar.vue'
import AppHeader from '../../components/layout/AppHeader.vue'

const router = useRouter()
const search = ref('')

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

const filteredPets = computed(() => {
  if (!search.value.trim()) return pets.value
  const term = search.value.toLowerCase()
  return pets.value.filter((pet) =>
    pet.name.toLowerCase().includes(term) ||
    pet.owner.toLowerCase().includes(term)
  )
})

const goToDetail = (id) => {
  router.push(`/pets/${id}`)
}
</script>
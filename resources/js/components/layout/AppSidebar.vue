<template>
  <aside class="flex w-64 flex-col border-r border-slate-200 bg-white">
    <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4">
      <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#10B981] text-sm font-bold text-white">
        V
      </div>
      <div>
        <div class="text-lg font-bold text-slate-900">VetFlow</div>
      </div>
    </div>

    <nav class="flex-1 space-y-1 px-3 py-4">
      <router-link
        v-for="item in menu"
        :key="item.name"
        :to="item.to"
        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
        active-class="bg-slate-100 text-slate-900"
      >
        <span>{{ item.icon }}</span>
        <span>{{ item.label }}</span>
      </router-link>
    </nav>

    <div class="border-t border-slate-200 p-4">
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-700">
          {{ initials }}
        </div>
        <div class="min-w-0 flex-1">
          <div class="truncate text-sm font-semibold text-slate-900">{{ user.name }}</div>
          <div class="truncate text-xs text-slate-500">{{ user.role }}</div>
        </div>
      </div>

      <button
        type="button"
        @click="logout"
        class="mt-4 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
      >
        Sair
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const menu = [
  { name: 'Dashboard', label: 'Dashboard', icon: '🏠', to: '/dashboard' },
  { name: 'Tutores', label: 'Tutores', icon: '👥', to: '/tutores' },
  { name: 'Pets', label: 'Pets', icon: '🐾', to: '/pets' },
  { name: 'Agenda', label: 'Agenda', icon: '📅', to: '/agenda' },
  { name: 'Prontuarios', label: 'Prontuários', icon: '📋', to: '/prontuarios' }
]

const user = computed(() => auth.user)

const initials = computed(() => {
  const name = user.value?.name || 'Dr.'
  return name
    .split(' ')
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase() || '')
    .join('')
})

const logout = () => {
  auth.logout()
  router.push('/login')
}
</script>
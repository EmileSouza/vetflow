<template>
  <header class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">{{ title }}</h1>
    </div>

    <button
      v-if="actionLabel"
      type="button"
      @click="handleAction"
      class="rounded-lg bg-[#10B981] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#059669]"
    >
      {{ actionLabel }}
    </button>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  },
  actionLabel: {
    type: String,
    default: ''
  },
  actionRoute: {
    type: String,
    default: ''
  }
})

const router = useRouter()

const handleAction = () => {
  if (props.actionRoute) {
    router.push(props.actionRoute)
  }
}
</script>
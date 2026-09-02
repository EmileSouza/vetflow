<template>
  <div class="stat-card bg-white rounded-lg shadow-md p-4">
    <h2 class="text-xl font-semibold text-gray-800">{{ title }}</h2>
    <p class="text-3xl font-bold text-green-600">{{ value }}</p>
    <p class="text-sm text-gray-500">{{ description }}</p>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  description: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.stat-card {
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: scale(1.05);
}
</style>
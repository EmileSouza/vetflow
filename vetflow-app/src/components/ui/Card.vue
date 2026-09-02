<template>
  <div class="bg-white rounded-lg shadow-md p-4">
    <h2 class="text-lg font-semibold text-gray-800">{{ title }}</h2>
    <p class="mt-2 text-gray-600">{{ content }}</p>
    <slot></slot>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  content: {
    type: String,
    required: true
  }
})
</script>

<style scoped>
/* Add any specific styles for the Card component here */
</style>
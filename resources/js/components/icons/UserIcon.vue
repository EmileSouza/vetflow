<script setup>
defineProps({
  size: {
    type: [Number, String],
    default: 20
  }
})
</script>

<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    fill="currentColor"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
  >
    <!-- Cabeça -->
    <circle
      cx="12"
      cy="7"
      r="4"
    />

    <!-- Corpo -->
    <path
      d="M4 21V19C4 15.7 7.6 13 12 13C16.4 13 20 15.7 20 19V21H4Z"
    />
  </svg>
</template>
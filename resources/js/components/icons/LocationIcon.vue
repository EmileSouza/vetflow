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
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
  >
    <path
      d="M20 10C20 15 12 22 12 22C12 22 4 15 4 10C4 5.6 7.6 2 12 2C16.4 2 20 5.6 20 10Z"
      fill="currentColor"
    />

    <circle
      cx="12"
      cy="10"
      r="3"
      fill="white"
    />
  </svg>
</template>
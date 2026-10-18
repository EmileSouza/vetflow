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
    <rect
      x="3"
      y="5"
      width="18"
      height="16"
      rx="2"
      stroke="currentColor"
      stroke-width="2"
    />

    <path
      d="M7 3V7"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
    />

    <path
      d="M17 3V7"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
    />

    <path
      d="M3 10H21"
      stroke="currentColor"
      stroke-width="2"
    />

    <path
      d="M8 14H10M14 14H16M8 18H10M14 18H16"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
    />
  </svg>
</template>
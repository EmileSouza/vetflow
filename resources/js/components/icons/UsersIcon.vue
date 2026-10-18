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
    <!-- Pessoa principal -->
    <circle cx="9" cy="7" r="3.5" />

    <path
      d="M2.5 20V18.5C2.5 14.9 5.4 12 9 12C12.6 12 15.5 14.9 15.5 18.5V20H2.5Z"
    />

    <!-- Segunda pessoa -->
    <circle cx="16.5" cy="8" r="2.8" />

    <path
      d="M15.5 13C19.1 13 22 15.5 22 18.7V20H17.5V18.5C17.5 16.4 16.8 14.5 15.5 13Z"
    />
  </svg>
</template>
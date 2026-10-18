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
      d="M12 3L20 6V11C20 16 16.6 20.2 12 21.5C7.4 20.2 4 16 4 11V6L12 3Z"
      stroke="currentColor"
      stroke-width="2"
      stroke-linejoin="round"
    />

    <path
      d="M8.5 12L10.8 14.3L15.8 9.3"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>
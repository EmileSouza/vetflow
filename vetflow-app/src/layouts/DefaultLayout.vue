<template>
  <div class="flex">
    <AppSidebar />
    <div class="flex-1">
      <AppHeader />
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import AppSidebar from '../components/layout/AppSidebar.vue'
import AppHeader from '../components/layout/AppHeader.vue'
</script>

<style scoped>
/* Add any specific styles for the DefaultLayout here */
</style>
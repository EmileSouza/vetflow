<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const cidade = computed(() => {
  return route.query.cidade || 'sua região'
})
</script>

<template>
  <main class="search-results">
    <h1>Atendimento veterinário em {{ cidade }}</h1>

    <p>
      Encontre profissionais disponíveis para cuidar do seu pet.
    </p>
  </main>
</template>

<style scoped>
.search-results {
  min-height: 100vh;
  padding: 60px 24px;
  background: #f8fcfa;
}

.search-results h1 {
  margin: 0;

  color: #0b2942;
  font-size: 32px;
}

.search-results p {
  margin-top: 10px;

  color: #58718b;
  font-size: 16px;
}
</style>
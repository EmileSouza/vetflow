<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Gerenciamento de Pacientes</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <Card v-for="patient in patients" :key="patient.id" :patient="patient" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Card from '../components/ui/Card.vue'

const patients = ref([])

const fetchPatients = async () => {
  try {
    const response = await fetch('/api/patients')
    if (!response.ok) {
      throw new Error('Erro ao buscar pacientes')
    }
    patients.value = await response.json()
  } catch (error) {
    console.error(error)
  }
}

onMounted(fetchPatients)
</script>

<style scoped>
/* Estilos específicos para a página de pacientes podem ser adicionados aqui */
</style>
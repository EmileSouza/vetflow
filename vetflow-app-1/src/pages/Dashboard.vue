<template>
  <DefaultLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <StatCard title="Total de Pacientes" value="150" />
        <StatCard title="Consultas Hoje" value="10" />
        <StatCard title="Serviços Prestados" value="25" />
      </div>
      <div class="mt-6">
        <Card>
          <template #header>
            <h2 class="text-lg font-semibold">Últimas Consultas</h2>
          </template>
          <ul>
            <li class="border-b py-2">Consulta com João - 10:00</li>
            <li class="border-b py-2">Consulta com Maria - 11:30</li>
            <li class="border-b py-2">Consulta com Pedro - 14:00</li>
          </ul>
        </Card>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import DefaultLayout from '../layouts/DefaultLayout.vue'
import StatCard from '../components/ui/StatCard.vue'
import Card from '../components/ui/Card.vue'
</script>

<style scoped>
/* Adicione estilos específicos para o Dashboard aqui, se necessário */
</style>
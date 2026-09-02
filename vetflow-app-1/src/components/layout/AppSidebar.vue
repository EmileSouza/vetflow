<template>
  <aside class="w-64 bg-white border-r border-slate-200 h-full">
    <div class="p-4">
      <h2 class="text-lg font-bold text-slate-900">VetFlow</h2>
      <nav class="mt-4">
        <ul>
          <li>
            <router-link to="/dashboard" class="block py-2 text-slate-700 hover:bg-slate-100 rounded">
              Dashboard
            </router-link>
          </li>
          <li>
            <router-link to="/patients" class="block py-2 text-slate-700 hover:bg-slate-100 rounded">
              Pacientes
            </router-link>
          </li>
          <li>
            <router-link to="/services" class="block py-2 text-slate-700 hover:bg-slate-100 rounded">
              Serviços
            </router-link>
          </li>
          <li>
            <router-link to="/settings" class="block py-2 text-slate-700 hover:bg-slate-100 rounded">
              Configurações
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script setup>
// No script needed for now
</script>

<style scoped>
/* Add any specific styles for the sidebar here */
</style>
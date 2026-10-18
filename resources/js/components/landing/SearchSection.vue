<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

import CalendarIcon from '../icons/CalendarIcon.vue'
import SearchIcon from '../icons/SearchIcon.vue'
import LocationIcon from '../icons/LocationIcon.vue'

const router = useRouter()
const cidade = ref('')

const buscarAtendimento = () => {
  const cidadeInformada = cidade.value.trim()

  if (!cidadeInformada) {
    return
  }

  router.push({
    name: 'BuscarAtendimento',
    query: {
      cidade: cidadeInformada
    }
  })
}
</script>
<template>
<!-- BUSCAR ATENDIMENTO -->
<section id="buscar-atendimento" class="search-section">
  <div class="search-box">

    <div class="search-icon">
      <CalendarIcon />
    </div>

    <div class="search-content">
      <h2>Seu pet precisa de atendimento?</h2>

      <p>
        Encontre clínicas e veterinários disponíveis na sua região.
      </p>

      <div class="search-form">
        <div class="city-input">
          <span>
            <LocationIcon :size="19" />
          </span>

          <input
           v-model="cidade"
           type="text"
           placeholder="Digite sua cidade"
           @keyup.enter="buscarAtendimento"
          />
        </div>
         <button
           type="button"
           class="search-button"
           @click="buscarAtendimento"
          >
          <SearchIcon :size="18" />
           Buscar atendimento
         </button>>
      </div>
    </div>

  </div>
</section>
</template>
<style scoped>
 /* BUSCAR ATENDIMENTO */

.search-section {
  padding: 0 24px 45px;
  background: #ffffff;
}

.search-box {
  max-width: 1200px;
  margin: 0 auto;
  padding: 28px 45px;

  display: flex;
  align-items: center;
  gap: 30px;

  background:
    linear-gradient(
      90deg,
      #eefaf6 0%,
      #f7fcfa 50%,
      #e7f7f2 100%
    );

  border-radius: 15px;
}

.search-icon {
  width: 65px;
  height: 65px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  background: #d9f3eb;
  border-radius: 50%;

  font-size: 28px;
}

.search-content {
  width: 100%;
}

.search-content h2 {
  margin: 0 0 5px;

  color: #0b2942;
  font-size: 24px;
  font-weight: 700;
}

.search-content > p {
  margin: 0 0 18px;

  color: #58718b;
  font-size: 14px;
}

.search-form {
  display: flex;
  align-items: center;
  gap: 16px;
}

.city-input {
  height: 52px;
  flex: 1;

  display: flex;
  align-items: center;
  gap: 12px;

  padding: 0 17px;

  background: #ffffff;
  border: 1px solid #dce5e2;
  border-radius: 9px;
}

.city-input span {
  font-size: 20px;
}

.city-input input {
  width: 100%;

  border: none;
  outline: none;

  color: #0b2942;
  font-size: 14px;
  background: transparent;
}

.city-input input::placeholder {
  color: #8293a3;
}

.search-button {
  height: 52px;
  min-width: 290px;
  padding: 0 25px;

  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;

  background: #009b72;
  border: none;
  border-radius: 9px;

  color: #ffffff;
  font-size: 14px;
  font-weight: 600;

  cursor: pointer;
  transition: 0.2s;
}

.search-button:hover {
  background: #008562;
}
</style>
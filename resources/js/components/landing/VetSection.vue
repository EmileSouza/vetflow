<script setup>
import UsersIcon from '../icons/UsersIcon.vue'
</script>
<template>
<!-- ÁREA PARA VETERINÁRIOS -->
<section id="veterinarios" class="vet-section">
  <div class="vet-box">

    <div class="vet-info">

      <div class="vet-icon">
        <UsersIcon :size="29" />
      </div>

      <div>
        <h2>É veterinário?</h2>

        <p>
          Faça parte do VetFlow e gerencie seus horários,
          atendimentos e pacientes em um só lugar.
        </p>
      </div>

    </div>

    <div class="vet-actions">

      <router-link to="/cadastro" class="vet-register-button">
        Cadastrar-se
      </router-link>

      <router-link to="/login" class="vet-login-link">
        Já tenho uma conta
      </router-link>

    </div>

  </div>
</section>
</template>
<style scoped>
/* ÁREA PARA VETERINÁRIOS */

.vet-section {
  padding: 0 24px 30px;
  background: #ffffff;
}

.vet-box {
  max-width: 1200px;
  margin: 0 auto;
  padding: 28px 45px;

  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 30px;

  background: linear-gradient(
    90deg,
    #eefaf6 0%,
    #f8fcfa 50%,
    #e8f7f2 100%
  );

  border-radius: 15px;
}

.vet-info {
  display: flex;
  align-items: center;
  gap: 22px;
}

.vet-icon {
  width: 62px;
  height: 62px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  background: #d9f3eb;
  border-radius: 50%;

  font-size: 27px;
}

.vet-info h2 {
  margin: 0 0 6px;

  color: #0b2942;
  font-size: 25px;
  font-weight: 700;
}

.vet-info p {
  max-width: 520px;
  margin: 0;

  color: #58718b;
  font-size: 14px;
  line-height: 1.5;
}

.vet-actions {
  min-width: 220px;

  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.vet-register-button {
  width: 100%;
  padding: 13px 25px;

  background: #009b72;
  border: 1px solid #009b72;
  border-radius: 9px;

  color: #ffffff;
  font-size: 14px;
  font-weight: 600;
  text-align: center;
  text-decoration: none;

  transition: 0.2s;
}

.vet-register-button:hover {
  background: #008562;
}

.vet-login-link {
  color: #58718b;
  font-size: 12px;
  text-decoration: underline;
}

.vet-login-link:hover {
  color: #009b72;
}
</style>
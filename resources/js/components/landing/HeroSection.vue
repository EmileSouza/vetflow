<script setup>
import SearchIcon from '../icons/SearchIcon.vue'
import UserIcon from '../icons/UserIcon.vue'
import ShieldIcon from '../icons/ShieldIcon.vue'
import CalendarIcon from '../icons/CalendarIcon.vue'
import HeartIcon from '../icons/HeartIcon.vue'
</script>
<template>
         <!-- HERO -->
  <section class="hero">
  <div class="hero-container">

    <!-- Lado esquerdo -->
    <div class="hero-content">

      <span class="hero-badge">
        Cuidado veterinário mais acessível
      </span>

      <h1>
        Encontre atendimento<br>
        para o seu pet de forma<br>
        <span>simples e rápida.</span>
      </h1>

      <p class="hero-description">
        O VetFlow conecta tutores a clínicas e veterinários,
        facilitando o agendamento de consultas e promovendo
        mais saúde para os pets.
      </p>

      <div class="hero-buttons">
        <a href="#buscar-atendimento" class="primary-button">
          <SearchIcon :size="19" />
          Encontrar atendimento
        </a>

        <router-link to="/cadastro" class="secondary-button">
          <UserIcon :size="19" />
          Sou veterinário
        </router-link>
      </div>

      <div class="hero-benefits">

        <div class="benefit">
          <span class="benefit-icon">
             <ShieldIcon :size="23" />
          </span>
          <p>
            Atendimento<br>
            com segurança
          </p>
        </div>

        <div class="benefit">
          <span class="benefit-icon">
             <CalendarIcon :size="22" />
          </span>
          <p>
            Agendamento<br>
            mais prático
          </p>
        </div>

        <div class="benefit">
          <span class="benefit-icon">
            <HeartIcon :size="22" />
          </span>
          <p>
            Mais saúde<br>
            para o seu pet
          </p>
        </div>

      </div>
    </div>

    <!-- Card do lado direito -->
    <div class="hero-card">
      <div class="card-detail"></div>

      <h2>
        Saúde e bem-estar<br>
        a um clique
      </h2>

      <p>
        Estamos aqui para tornar o cuidado
        com o seu pet mais simples, organizado
        e acessível.
      </p>

      <div class="card-line"></div>

      <em>
        “Pets saudáveis, tutores mais tranquilos.”
      </em>

      <strong>VetFlow</strong>
    </div>

  </div>
</section>

    </template>
<style scoped>
 /* HERO */
.hero {
  position: relative;
  overflow: hidden;
  background:
    radial-gradient(circle at 90% 15%, rgba(0, 155, 114, 0.12), transparent 28%),
    radial-gradient(circle at 67% 90%, rgba(0, 155, 114, 0.10), transparent 25%),
    linear-gradient(90deg, #ffffff 0%, #f4fbf8 100%);
}

.hero-container {
  max-width: 1200px;
  min-height: 510px;
  margin: 0 auto;
  padding: 55px 24px;

  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 70px;
}

.hero-content {
  flex: 1;
  max-width: 610px;
}

.hero-badge {
  display: inline-block;
  margin-bottom: 20px;
  padding: 7px 14px;

  background: #dff5ed;
  border-radius: 20px;

  color: #08795e;
  font-size: 12px;
  font-weight: 500;
}

.hero-content h1 {
  margin: 0;

  color: #0b2942;
  font-size: 46px;
  line-height: 1.08;
  font-weight: 700;
}

.hero-content h1 span {
  color: #009b72;
}

.hero-description {
  max-width: 560px;
  margin: 18px 0 25px;

  color: #58718b;
  font-size: 16px;
  line-height: 1.55;
}

.hero-buttons {
  display: flex;
  gap: 15px;
}

.primary-button,
.secondary-button {
  min-height: 48px;
  padding: 0 25px;

  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;

  border-radius: 9px;

  font-size: 14px;
  font-weight: 600;
  text-decoration: none;

  transition: 0.2s;
}

.primary-button {
  background: #009b72;
  border: 1px solid #009b72;
  color: #ffffff;
}

.primary-button:hover {
  background: #008562;
}

.secondary-button {
  background: #ffffff;
  border: 1px solid #009b72;
  color: #008f69;
}

.secondary-button:hover {
  background: #effaf6;
}

.hero-benefits {
  display: flex;
  gap: 38px;
  margin-top: 35px;
}

.benefit {
  display: flex;
  align-items: center;
  gap: 10px;
}

.benefit-icon {
  width: 38px;
  height: 38px;

  display: flex;
  align-items: center;
  justify-content: center;

  background: #e4f6ef;
  border-radius: 50%;

  color: #009b72;
  font-size: 20px;
  font-weight: 700;
}

.benefit p {
  margin: 0;

  color: #58718b;
  font-size: 12px;
  line-height: 1.35;
}

/* CARD DO HERO */

.hero-card {
  width: 390px;
  padding: 48px 45px;

  background: rgba(255, 255, 255, 0.48);
  border: 1px solid rgba(255, 255, 255, 0.7);
  border-radius: 24px;

  backdrop-filter: blur(10px);
}

.card-detail {
  width: 48px;
  height: 5px;
  margin-bottom: 20px;

  background: #9bdcc8;
  border-radius: 10px;
}

.hero-card h2 {
  margin: 0 0 17px;

  color: #0b2942;
  font-size: 25px;
  line-height: 1.2;
}

.hero-card p {
  margin: 0;

  color: #58718b;
  font-size: 15px;
  line-height: 1.55;
}

.card-line {
  width: 50px;
  height: 5px;
  margin: 27px 0;

  background: #0b4962;
  border-radius: 10px;
}

.hero-card em {
  display: block;

  color: #58718b;
  font-size: 14px;
}

.hero-card strong {
  display: block;
  margin-top: 7px;

  color: #58718b;
  font-size: 14px;
}
</style>
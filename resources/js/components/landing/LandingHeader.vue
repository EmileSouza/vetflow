     <script setup>
     import VetFlowLogo from '../common/VetFlowLogo.vue'
    </script>    
    <template> 
    <!-- HEADER -->
    <header class="header">
      <div class="header-container">

        <!-- Logo -->
    <a href="#" class="brand">
       <VetFlowLogo :size="44" />
    </a>

        <!-- Menu -->
        <nav class="nav-menu">
          <a href="#como-funciona">Como funciona</a>
          <a href="#veterinarios">Para veterinários</a>
          <a href="#duvidas">Dúvidas</a>
          <a href="#contato">Contato</a>
        </nav>

        <!-- Login -->
        <router-link to="/login" class="login-button">
          Entrar
        </router-link>

      </div>
    </header>
    </template>

    <style scoped>
.landing-page {
  min-height: 100vh;
  background: #ffffff;
  color: #0b2942;
}

.header {
  width: 100%;
  background: #ffffff;
  border-bottom: 1px solid #eef2f1;
}

.header-container {
  max-width: 1200px;
  height: 72px;
  margin: 0 auto;
  padding: 0 24px;

  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo-icon {
  width: 42px;
  height: 42px;
  border-radius: 50% 50% 50% 10px;

  display: flex;
  align-items: center;
  justify-content: center;

  background: #009b72;
  color: white;
  font-size: 21px;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-name {
  color: #0b2942;
  font-size: 25px;
  font-weight: 700;
  line-height: 1;
}

.logo-name span {
  color: #009b72;
}

.logo-text small {
  margin-top: 4px;
  color: #718096;
  font-size: 10px;
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: 38px;
}

.nav-menu a {
  color: #536b84;
  font-size: 14px;
  text-decoration: none;
  transition: 0.2s;
}

.nav-menu a:hover {
  color: #009b72;
}

.login-button {
  min-width: 105px;
  padding: 11px 24px;

  border: 1px solid #009b72;
  border-radius: 9px;

  color: #008f69;
  font-size: 14px;
  font-weight: 600;
  text-align: center;
  text-decoration: none;

  transition: 0.2s;
}

.login-button:hover {
  background: #009b72;
  color: #ffffff;
}

</style>
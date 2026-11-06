<script setup>
import VetFlowLogo from '../common/VetFlowLogo.vue'
import ArrowUpIcon from '../icons/ArrowUpIcon.vue'
</script>

<template>
  <footer id="contato" class="footer">
    <div class="footer-container">

      <!-- Logo -->
      <div class="footer-brand">
        <VetFlowLogo :size="44" />
      </div>

      <!-- Links -->
      <nav class="footer-links">
        <a href="#">Sobre</a>
        <a href="#como-funciona">Como funciona</a>
        <a href="#veterinarios">Para veterinários</a>
        <a href="#duvidas">Dúvidas</a>
        <a href="#contato">Contato</a>
      </nav>

      <!-- Copyright e acesso administrativo -->
      <div class="footer-copyright">
        <p>© 2026 VetFlow.</p>
        <p>Todos os direitos reservados.</p>

        <RouterLink
          to="/admin/login"
          class="admin-link"
          aria-label="Acessar área administrativa"
        >
          <span aria-hidden="true">🔒</span>
          Área administrativa
        </RouterLink>
      </div>

      <!-- Voltar ao topo -->
      <a
        href="#"
        class="back-to-top"
        aria-label="Voltar ao topo"
        title="Voltar ao topo"
      >
        <ArrowUpIcon :size="17" />
      </a>

    </div>
  </footer>
</template>

<style scoped>
.footer {
  background: #ffffff;
  border-top: 1px solid #edf1f0;
}

.footer-container {
  max-width: 1200px;
  min-height: 100px;
  margin: 0 auto;
  padding: 22px 10px;

  display: grid;
  grid-template-columns: 250px 1fr 175px 38px;
  align-items: center;
  column-gap: 22px;
}

/* LINKS */

.footer-links {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 34px;
  white-space: nowrap;
}

.footer-links a {
  color: #58718b;
  font-size: 11px;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #009b72;
}

/* COPYRIGHT */

.footer-copyright {
  justify-self: end;
  color: #718096;
  font-size: 10px;
  line-height: 1.45;
  white-space: nowrap;
}

.footer-copyright p {
  margin: 0;
}

/* ACESSO ADMINISTRATIVO */

.admin-link {
  display: inline-flex;
  align-items: center;
  justify-content: flex-end;
  gap: 5px;
  margin-top: 7px;

  color: #718096;
  font-size: 10px;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s;
}

.admin-link:hover {
  color: #009b72;
  text-decoration: underline;
}

.admin-link:focus-visible {
  outline: 2px solid #009b72;
  outline-offset: 4px;
  border-radius: 3px;
}

/* SETA */

.back-to-top {
  width: 34px;
  height: 34px;
  justify-self: end;

  display: flex;
  align-items: center;
  justify-content: center;

  background: #e4f6ef;
  border-radius: 7px;
  color: #009b72;
  text-decoration: none;

  transition:
    background 0.2s,
    color 0.2s;
}

.back-to-top:hover {
  background: #d7f1e8;
  color: #008562;
}

/* RESPONSIVIDADE */

@media (max-width: 1000px) {
  .footer-container {
    grid-template-columns: 1fr 38px;
    row-gap: 20px;
    padding: 24px;
  }

  .footer-brand {
    grid-column: 1;
    grid-row: 1;
  }

  .footer-links {
    grid-column: 1 / -1;
    grid-row: 2;
    flex-wrap: wrap;
    justify-content: flex-start;
    gap: 14px 22px;
    white-space: normal;
  }

  .footer-copyright {
    grid-column: 1 / -1;
    grid-row: 3;
    justify-self: start;
  }

  .admin-link {
    justify-content: flex-start;
  }

  .back-to-top {
    grid-column: 2;
    grid-row: 1;
  }
}

@media (max-width: 480px) {
  .footer-container {
    padding: 24px 16px;
  }

  .footer-links {
    gap: 12px 18px;
  }
}
</style>

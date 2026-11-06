<script setup>
import { onMounted, ref } from 'vue'
import {
  loginAdmin,
  logoutAdmin,
  obterAdmin
} from '../services/adminApi'

const email = ref('')
const senha = ref('')
const exibirSenha = ref(false)
const carregando = ref(false)
const verificandoSessao = ref(true)
const usuario = ref(null)
const erro = ref('')

onMounted(async () => {
  try {
    const resposta = await obterAdmin()
    usuario.value = resposta.data
  } catch {
    // Visitantes não possuem sessão administrativa.
    usuario.value = null
  } finally {
    verificandoSessao.value = false
  }
})

async function entrar() {
  if (carregando.value) return

  erro.value = ''
  carregando.value = true

  try {
    const resposta = await loginAdmin(
      email.value.trim(),
      senha.value
    )

    usuario.value = resposta.data.usuario
    senha.value = ''
  } catch (error) {
    const status = error.response?.status

    if (status === 401) {
      erro.value = 'E-mail ou senha inválidos, ou conta sem autorização.'
    } else if (status === 429) {
      erro.value = 'Muitas tentativas. Tente novamente mais tarde.'
    } else if (status === 419) {
      erro.value = 'Sua sessão expirou. Atualize a página e tente novamente.'
    } else {
      erro.value = 'Não foi possível realizar o login. Tente novamente.'
    }
  } finally {
    carregando.value = false
  }
}

async function sair() {
  if (carregando.value) return

  carregando.value = true
  erro.value = ''

  try {
    await logoutAdmin()
    usuario.value = null
    senha.value = ''
    email.value = ''
  } catch {
    erro.value = 'Não foi possível encerrar a sessão. Tente novamente.'
  } finally {
    carregando.value = false
  }
}
</script>

<template>
  <main class="admin-page">
    <div class="admin-shell">
      <!-- Painel institucional -->
      <section class="brand-panel">
        <RouterLink to="/" class="brand-link">
          <span class="brand-icon">✦</span>
          <span>VetFlow <strong>Admin</strong></span>
        </RouterLink>

        <div class="brand-content">
          <div class="eyebrow">
            <span class="eyebrow-dot"></span>
            CENTRAL ADMINISTRATIVA
          </div>

          <h1>
            Gestão com clareza.
            <span>Controle com confiança.</span>
          </h1>

          <p>
            Um espaço exclusivo para acompanhar,
            administrar e cuidar da evolução do VetFlow.
          </p>

          <div class="feature-list">
            <div class="feature">
              <span class="feature-number">01</span>
              <span>Gestão de profissionais</span>
            </div>

            <div class="feature">
              <span class="feature-number">02</span>
              <span>Verificação de cadastros</span>
            </div>

            <div class="feature">
              <span class="feature-number">03</span>
              <span>Indicadores da plataforma</span>
            </div>
          </div>
        </div>

        <p class="brand-footer">
          VetFlow © 2026 · Ambiente administrativo
        </p>
      </section>

      <!-- Formulário -->
      <section class="form-panel">
        <div class="form-content">
          <div v-if="verificandoSessao" class="loading-state" role="status">
            Verificando acesso administrativo...
          </div>

          <template v-else-if="!usuario">
            <div class="form-heading">
              <span class="section-tag">ACESSO RESTRITO</span>

              <h2>Bem-vinda à <span>Central Admin.</span></h2>

              <p>
                Entre com suas credenciais autorizadas
                para acessar a administração.
              </p>
            </div>

            <form class="login-form" @submit.prevent="entrar">
              <div v-if="erro" class="error-message" role="alert">
                {{ erro }}
              </div>

              <div class="field">
                <label for="admin-email">E-mail</label>
                <input
                  id="admin-email"
                  v-model.trim="email"
                  type="email"
                  autocomplete="username"
                  placeholder="seuemail@exemplo.com"
                  maxlength="255"
                  required
                />
              </div>

              <div class="field">
                <label for="admin-password">Senha</label>

                <div class="password-field">
                  <input
                    id="admin-password"
                    v-model="senha"
                    :type="exibirSenha ? 'text' : 'password'"
                    autocomplete="current-password"
                    placeholder="Digite sua senha"
                    required
                  />

                  <button
                    type="button"
                    class="password-toggle"
                    :aria-label="exibirSenha ? 'Ocultar senha' : 'Mostrar senha'"
                    @click="exibirSenha = !exibirSenha"
                  >
                    {{ exibirSenha ? 'Ocultar' : 'Mostrar' }}
                  </button>
                </div>
              </div>

              <button
                class="submit-button"
                type="submit"
                :disabled="carregando"
              >
                {{ carregando ? 'Verificando credenciais...' : 'Entrar na Central Admin' }}
                <span aria-hidden="true">→</span>
              </button>
            </form>

            <p class="security-note">
              🔒 Acesso exclusivo para contas autorizadas.
            </p>

            <RouterLink to="/" class="back-link">
              ← Voltar ao VetFlow
            </RouterLink>
          </template>

          <!-- Estado temporário após login -->
          <template v-else>
            <div class="success-content">
              <div class="success-icon">✓</div>

              <span class="section-tag">ACESSO AUTORIZADO</span>

              <h2>Bem-vinda à Central Admin!</h2>

              <p>
                Sua sessão administrativa foi iniciada com sucesso.
              </p>

              <div class="account-info">
                <div>
                  <span>Conta</span>
                  <strong>{{ usuario.nome }}</strong>
                </div>

                <div>
                  <span>Perfil de acesso</span>
                  <strong>
                    {{ usuario.papel === 'admin' ? 'Administradora' : 'Investidor' }}
                  </strong>
                </div>
              </div>

              <p class="next-info">
                O Dashboard administrativo será implementado
                na próxima etapa do projeto.
              </p>

              <div v-if="erro" class="error-message" role="alert">
                {{ erro }}
              </div>

              <button
                class="submit-button"
                type="button"
                :disabled="carregando"
                @click="sair"
              >
                {{ carregando ? 'Encerrando...' : 'Encerrar sessão' }}
              </button>
            </div>
          </template>
        </div>
      </section>
    </div>
  </main>
</template>

<style scoped>
.admin-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  background:
    radial-gradient(circle at 10% 10%, #d6e9db 0%, transparent 35%),
    linear-gradient(135deg, #edf4ef, #f8faf8);
  font-family: inherit;
}

.admin-shell {
  width: 100%;
  max-width: 1120px;
  min-height: 650px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  border-radius: 28px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 24px 80px rgba(8, 47, 37, 0.13);
}

.brand-panel {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  overflow: hidden;
  padding: 44px;
  color: #fff;
  background:
    radial-gradient(circle at 85% 15%, rgba(90, 187, 138, 0.24), transparent 42%),
    linear-gradient(150deg, #103e32, #092b25 75%);
}

.brand-panel::after {
  content: '';
  position: absolute;
  width: 340px;
  height: 340px;
  right: -190px;
  bottom: -180px;
  border: 1px solid rgba(185, 238, 195, 0.18);
  border-radius: 50%;
  pointer-events: none;
}

.brand-link {
  position: relative;
  z-index: 1;
  display: inline-flex;
  align-items: center;
  gap: 12px;
  width: fit-content;
  color: #fff;
  text-decoration: none;
  font-size: 23px;
  font-weight: 700;
}

.brand-link strong {
  color: #a4e4ba;
}

.brand-icon {
  width: 43px;
  height: 43px;
  display: grid;
  place-items: center;
  border-radius: 13px;
  color: #0d4536;
  background: #b6eac5;
  font-size: 24px;
}

.brand-content {
  position: relative;
  z-index: 1;
  margin: 70px 0;
}

.eyebrow {
  display: flex;
  align-items: center;
  gap: 9px;
  color: #b3dfc4;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2px;
}

.eyebrow-dot {
  height: 7px;
  width: 7px;
  background: #8fdfad;
  border-radius: 50%;
}

.brand-content h1 {
  margin: 24px 0 20px;
  font-size: clamp(34px, 4vw, 45px);
  line-height: 1.15;
  letter-spacing: -1.3px;
  font-weight: 750;
}

.brand-content h1 span {
  display: block;
  color: #a4e4ba;
}

.brand-content > p {
  max-width: 390px;
  font-size: 15px;
  line-height: 1.85;
  color: #c4dcd2;
}

.feature-list {
  display: grid;
  gap: 18px;
  margin-top: 40px;
}

.feature {
  display: flex;
  align-items: center;
  gap: 14px;
  color: #edf7f0;
  font-size: 14px;
}

.feature-number {
  width: 34px;
  height: 34px;
  display: grid;
  place-items: center;
  border-radius: 10px;
  background: rgba(190, 241, 207, 0.1);
  border: 1px solid rgba(190, 241, 207, 0.17);
  color: #b6eac5;
  font-size: 12px;
  font-weight: 700;
}

.brand-footer {
  position: relative;
  z-index: 1;
  font-size: 12px;
  color: #9fc1b1;
}

.form-panel {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 56px 50px;
}

.form-content {
  width: 100%;
  max-width: 385px;
}

.section-tag {
  color: #198066;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 1.6px;
}

.form-heading h2,
.success-content h2 {
  margin: 15px 0 13px;
  color: #153b30;
  font-size: 33px;
  line-height: 1.2;
  letter-spacing: -0.8px;
}

.form-heading h2 span {
  color: #188363;
}

.form-heading p,
.success-content > p {
  color: #698078;
  font-size: 14px;
  line-height: 1.8;
}

.login-form {
  display: grid;
  gap: 22px;
  margin-top: 36px;
}

.field {
  display: grid;
  gap: 9px;
}

.field label {
  color: #26463a;
  font-size: 13px;
  font-weight: 700;
}

.field input {
  width: 100%;
  min-width: 0;
  height: 51px;
  padding: 0 16px;
  border: 1px solid #dbe7df;
  border-radius: 12px;
  background: #fbfdfb;
  color: #183a30;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.field input:focus {
  border-color: #24876a;
  box-shadow: 0 0 0 4px rgba(36, 135, 106, 0.1);
}

.password-field {
  position: relative;
}

.password-field input {
  padding-right: 85px;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: transparent;
  color: #167659;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
}

.submit-button {
  display: flex;
  width: 100%;
  align-items: center;
  justify-content: center;
  gap: 12px;
  min-height: 52px;
  padding: 12px 16px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(115deg, #136849, #0c483a);
  color: #fff;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(13, 93, 64, 0.2);
}

.submit-button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.security-note {
  margin: 22px 0 26px;
  color: #72877c;
  text-align: center;
  font-size: 12px;
}

.back-link {
  display: block;
  width: fit-content;
  margin: 0 auto;
  color: #167659;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
}

.back-link:hover {
  text-decoration: underline;
}

.error-message {
  padding: 13px 15px;
  border-radius: 10px;
  background: #fff0ed;
  border: 1px solid #f8ccc4;
  color: #aa3528;
  font-size: 13px;
  line-height: 1.6;
}

.loading-state {
  color: #587367;
  text-align: center;
}

.success-content {
  text-align: center;
}

.success-icon {
  display: grid;
  place-items: center;
  width: 68px;
  height: 68px;
  margin: 0 auto 24px;
  border-radius: 22px;
  background: #e2f4e7;
  color: #167659;
  font-size: 34px;
  font-weight: bold;
}

.account-info {
  display: grid;
  gap: 16px;
  margin: 30px 0;
  padding: 22px;
  border: 1px solid #e0ebe3;
  border-radius: 16px;
  text-align: left;
  background: #f8fbf8;
}

.account-info div {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.account-info span {
  color: #74897e;
  font-size: 12px;
}

.account-info strong {
  color: #153b30;
  font-size: 15px;
}

.next-info {
  margin-bottom: 24px;
}

@media (max-width: 800px) {
  .admin-shell {
    grid-template-columns: 1fr;
  }

  .brand-panel {
    padding: 28px;
  }

  .brand-content {
    margin: 35px 0 15px;
  }

  .brand-content h1 {
    font-size: 32px;
  }

  .feature-list,
  .brand-footer {
    display: none;
  }

  .form-panel {
    padding: 40px 25px;
  }
}

@media (max-width: 430px) {
  .admin-page {
    padding: 0;
    align-items: stretch;
  }

  .admin-shell {
    border-radius: 0;
    min-height: 100vh;
  }

  .brand-panel {
    padding: 24px;
  }

  .brand-content h1 {
    font-size: 28px;
  }

  .brand-content > p {
    font-size: 13px;
  }

  .form-heading h2,
  .success-content h2 {
    font-size: 28px;
  }
}
</style>

<script setup>
import { computed, nextTick, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import adminApi, {
  obterAdmin,
  logoutAdmin
} from '../services/adminApi'
import AdminAgendamentosChart from '../components/Admin/AdminAgendamentosChart.vue'

const router = useRouter()
const graficoAgendamentos = ref(null)

const usuario = ref(null)
const resumo = ref(null)
const pendentes = ref([])
const paginaFila = ref(1)
const ultimaPaginaFila = ref(1)
const totalFila = ref(0)
const mostrarFilaCompleta = ref(true)

const carregando = ref(true)
const carregandoFila = ref(false)
const saindo = ref(false)

const erro = ref('')
const erroFila = ref('')
const aviso = ref('')

const modalAberto = ref(false)
const veterinarioSelecionado = ref(null)
const tipoDecisao = ref('aprovar')
const ufCrmv = ref('')
const fonte = ref('')
const consultaConfirmada = ref(false)
const motivoRejeicao = ref('')
const salvandoDecisao = ref(false)
const erroDecisao = ref('')

const ehAdministrador = computed(
  () => usuario.value?.papel === 'admin'
)

const indicadoresPrincipais = computed(() => [
  {
    titulo: 'Veterinários',
    valor: resumo.value?.veterinarios_total,
    icone: '♙'
  },
  {
    titulo: 'CRMV pendentes',
    valor: resumo.value?.crmv_pendentes,
    icone: '◷'
  },
  {
    titulo: 'CRMV aprovados',
    valor: resumo.value?.crmv_aprovados,
    icone: '✓'
  },
  {
    titulo: 'Agendamentos',
    valor: resumo.value?.agendamentos_total,
    icone: '▦'
  }
])

const indicadoresExtras = computed(() => [
  {
    titulo: 'Tutores cadastrados',
    valor: resumo.value?.tutores_total,
    icone: '♙'
  },
  {
    titulo: 'Pets cadastrados',
    valor: resumo.value?.pets_total,
    icone: '🐾'
  },
  {
    titulo: 'CRMVs rejeitados',
    valor: resumo.value?.crmv_rejeitados,
    icone: '✕'
  }
])

function numero(valor) {
  return typeof valor === 'number'
    ? new Intl.NumberFormat('pt-BR').format(valor)
    : '—'
}

function dataCadastro(valor) {
  if (!valor) return '—'

  const data = new Date(valor)

  return Number.isNaN(data.getTime())
    ? '—'
    : new Intl.DateTimeFormat('pt-BR').format(data)
}

function mensagemErro(error, padrao) {
  const status = error?.response?.status

  if (status === 419) {
    return 'A sessão expirou. Entre novamente no painel administrativo.'
  }

  if (status === 409) {
    return 'Este CRMV já foi analisado. Atualize a fila.'
  }

  if (status === 403) {
    return 'Sua conta não possui permissão para esta operação.'
  }

  if (status === 422) {
    const erros = error?.response?.data?.errors
    const primeira = erros && Object.values(erros)[0]

    return Array.isArray(primeira)
      ? primeira[0]
      : 'Confira as informações preenchidas.'
  }

  return error?.response?.data?.message || padrao
}

// FILA DE CRMV
async function carregarFila(pagina = 1) {
  if (!ehAdministrador.value) return

  carregandoFila.value = true
  erroFila.value = ''

  try {
    const resposta = await adminApi.get(
      '/admin/crmv/pendentes',
      {
        params: {
          page: pagina
        }
      }
    )

    pendentes.value = resposta.data.data || []

    paginaFila.value =
      resposta.data.meta?.pagina_atual ?? 1

    ultimaPaginaFila.value =
      resposta.data.meta?.ultima_pagina ?? 1

    totalFila.value =
      resposta.data.meta?.total ?? 0

  } catch (error) {
    pendentes.value = []

    erroFila.value = mensagemErro(
      error,
      'Não foi possível carregar a fila de CRMV.'
    )
  } finally {
    carregandoFila.value = false
  }
}

// DASHBOARD
async function carregarDashboard() {
  carregando.value = true
  erro.value = ''

  try {
    const respostaUsuario = await obterAdmin()
    usuario.value = respostaUsuario.data

    const respostaResumo = await adminApi.get(
      '/admin/dashboard/resumo'
    )

    resumo.value = respostaResumo.data.data

    if (ehAdministrador.value) {
      await carregarFila(paginaFila.value)
    } else {
      pendentes.value = []
      mostrarFilaCompleta.value = false
    }

  } catch (error) {
    if (
      [401, 403, 419].includes(
        error?.response?.status
      )
    ) {
      await router.replace({
        name: 'AdminLogin'
      })
      return
    }

    erro.value = mensagemErro(
      error,
      'Não foi possível carregar os dados do Dashboard.'
    )
  } finally {
    carregando.value = false
  }
}

// IR PARA A FILA COMPLETA
async function abrirFilaCompleta() {
  mostrarFilaCompleta.value = true

  if (paginaFila.value !== 1) {
    await carregarFila(1)
  }

  await nextTick()

  document
    .getElementById('fila-completa')
    ?.scrollIntoView({
      behavior: 'smooth'
    })
}

// MODAL DE APROVAÇÃO OU REJEIÇÃO
function abrirDecisao(vet, tipo) {
  if (!ehAdministrador.value) return

  veterinarioSelecionado.value = vet
  tipoDecisao.value = tipo

  ufCrmv.value = vet.crmv_uf || ''
  fonte.value = ''
  consultaConfirmada.value = false
  motivoRejeicao.value = ''
  erroDecisao.value = ''

  modalAberto.value = true
}

function fecharDecisao() {
  if (!salvandoDecisao.value) {
    modalAberto.value = false
  }
}

// APROVAR OU REJEITAR CRMV
async function confirmarDecisao() {
  if (
    !ehAdministrador.value ||
    !veterinarioSelecionado.value ||
    salvandoDecisao.value
  ) {
    return
  }

  erroDecisao.value = ''

  const id = veterinarioSelecionado.value.id
  let dados

  if (tipoDecisao.value === 'aprovar') {
    if (!/^[A-Za-z]{2}$/.test(ufCrmv.value.trim())) {
      erroDecisao.value =
        'Informe a UF do registro CRMV com duas letras.'
      return
    }

    if (
      !fonte.value.trim() ||
      !consultaConfirmada.value
    ) {
      erroDecisao.value =
        'Informe a fonte oficial e confirme que realizou a consulta.'
      return
    }

    dados = {
      metodo_verificacao: 'manual',
      crmv_uf: ufCrmv.value.trim().toUpperCase(),
      fonte_verificacao: fonte.value.trim(),
      consulta_confirmada: true
    }

  } else {
    if (motivoRejeicao.value.trim().length < 10) {
      erroDecisao.value =
        'Informe uma justificativa com pelo menos 10 caracteres.'
      return
    }

    dados = {
      motivo_rejeicao_crmv:
        motivoRejeicao.value.trim(),

      ...(
        fonte.value.trim()
          ? {
              fonte_verificacao: fonte.value.trim()
            }
          : {}
      )
    }
  }

  salvandoDecisao.value = true

  try {
    await adminApi.patch(
      `/admin/crmv/${id}/${tipoDecisao.value}`,
      dados
    )

    modalAberto.value = false

    aviso.value =
      tipoDecisao.value === 'aprovar'
        ? 'CRMV aprovado e decisão registrada com sucesso.'
        : 'CRMV rejeitado e justificativa registrada.'

    paginaFila.value = 1

    await atualizarDashboardCompleto()

  } catch (error) {
    erroDecisao.value = mensagemErro(
      error,
      'Não foi possível registrar a decisão.'
    )
  } finally {
    salvandoDecisao.value = false
  }
}

// LOGOUT
async function sair() {
  if (saindo.value) return

  saindo.value = true
  erro.value = ''

  try {
    await logoutAdmin()

    await router.replace({
      name: 'AdminLogin'
    })

  } catch (error) {
    erro.value = mensagemErro(
      error,
      'Não foi possível encerrar a sessão.'
    )
  } finally {
    saindo.value = false
  }
}

// ATUALIZAR INDICADORES, FILA E GRÁFICO
async function atualizarDashboardCompleto() {
  await carregarDashboard()

  if (
    usuario.value &&
    graficoAgendamentos.value
  ) {
    await graficoAgendamentos.value.recarregar()
  }
}

onMounted(carregarDashboard)
</script>

<template>
  <div class="admin-dashboard">

    <!-- CABEÇALHO -->
    <header class="topbar">
      <div class="brand">
        <span class="brand-paw" aria-hidden="true">
          🐾
        </span>

        <strong>VetFlow Admin</strong>

        <span class="brand-separator"></span>

        <span class="brand-description">
          Plataforma Veterinária
        </span>
      </div>

      <div class="top-actions">
        <span class="search-upcoming">
          Pesquisa avançada em breve
        </span>

        <span
          v-if="usuario"
          class="user-pill"
        >
          <span class="avatar">
            {{
              usuario.nome?.charAt(0)?.toUpperCase()
              || 'A'
            }}
          </span>

          <span class="username">
            {{ usuario.nome }}
          </span>
        </span>

        <button
          class="exit-button"
          type="button"
          :disabled="saindo"
          @click="sair"
        >
          {{ saindo ? 'Saindo...' : 'Sair' }}
        </button>
      </div>
    </header>

    <!-- ESTRUTURA -->
    <div class="app-layout">

      <!-- MENU -->
      <aside
        class="sidebar"
        aria-label="Menu administrativo"
      >
        <nav class="side-links">

          <a
            href="#inicio"
            class="side-link selected"
          >
            <span>⌂</span>
            Dashboard
          </a>

          <!-- NOVO LINK PARA GESTÃO -->
          <RouterLink
            v-if="ehAdministrador"
            :to="{ name: 'AdminVeterinarios' }"
            class="side-link"
          >
            <span>♙</span>
            Veterinários
          </RouterLink>

          <span
            v-else
            class="side-link disabled"
          >
            <span>♙</span>
            Veterinários
            <small>Restrito</small>
          </span>

          <a
            v-if="ehAdministrador"
            href="#verificacao-crmv"
            class="side-link"
          >
            <span>♧</span>
            CRMV
          </a>

          <span class="side-link disabled">
            <span>▦</span>
            Agendamentos
            <small>Em breve</small>
          </span>

          <span class="side-link disabled">
            <span>▥</span>
            Relatórios
            <small>Em breve</small>
          </span>

          <div class="side-separator"></div>

          <span class="side-link disabled">
            <span>⚙</span>
            Configurações
            <small>Em breve</small>
          </span>
        </nav>

        <div class="side-footer">
          🐾
          <small>VetFlow © 2026</small>
        </div>
      </aside>

      <!-- CONTEÚDO -->
      <main id="inicio" class="content">

        <div class="page-heading">
          <div>
            <h1>Dashboard Administrativo</h1>

            <div class="intro">
              <h2>Visão geral</h2>

              <p>
                Acompanhe o status dos profissionais,
                as verificações de CRMV e os
                agendamentos da plataforma.
              </p>
            </div>
          </div>

          <button
            class="outline-button"
            type="button"
            :disabled="carregando"
            @click="atualizarDashboardCompleto"
          >
            {{
              carregando
                ? 'Carregando...'
                : 'Atualizar dados'
            }}
          </button>
        </div>

        <!-- MENSAGENS -->
        <p
          v-if="erro"
          class="alert error"
          role="alert"
        >
          {{ erro }}
        </p>

        <p
          v-if="aviso"
          class="alert success"
          role="status"
        >
          {{ aviso }}
        </p>

        <!-- INDICADORES PRINCIPAIS -->
        <section
          class="primary-metrics"
          aria-label="Indicadores principais"
        >
          <article
            v-for="item in indicadoresPrincipais"
            :key="item.titulo"
            class="metric-card"
          >
            <span
              class="metric-symbol"
              aria-hidden="true"
            >
              {{ item.icone }}
            </span>

            <div class="metric-text">
              <p>{{ item.titulo }}</p>

              <strong>
                {{
                  carregando
                    ? '...'
                    : numero(item.valor)
                }}
              </strong>
            </div>
          </article>
        </section>

        <!-- INDICADORES COMPLEMENTARES -->
        <section
          class="secondary-metrics"
          aria-label="Indicadores complementares"
        >
          <article
            v-for="item in indicadoresExtras"
            :key="item.titulo"
            class="compact-metric"
          >
            <span
              class="compact-symbol"
              aria-hidden="true"
            >
              {{ item.icone }}
            </span>

            <div>
              <p>{{ item.titulo }}</p>

              <strong>
                {{
                  carregando
                    ? '...'
                    : numero(item.valor)
                }}
              </strong>
            </div>
          </article>
        </section>

        <!-- GRÁFICO E CRMV -->
        <div class="content-columns">

          <!-- GRÁFICO COM DADOS REAIS -->
          <AdminAgendamentosChart
            v-if="usuario"
            ref="graficoAgendamentos"
          />

          <!-- VERIFICAÇÃO CRMV -->
          <section
            id="verificacao-crmv"
            class="panel crmv-panel"
          >
            <div class="panel-heading">
              <div>
                <h3>Verificação de CRMV</h3>

                <p>
                  {{
                    ehAdministrador
                      ? 'Fila de profissionais aguardando análise.'
                      : 'Resumo das verificações da plataforma.'
                  }}
                </p>
              </div>

              <span
                class="shield-mark"
                aria-hidden="true"
              >
                ✓
              </span>
            </div>

            <div
              v-if="ehAdministrador"
              class="queue-content"
            >
              <div class="queue-counter">
                <span>Pendentes de análise</span>

                <strong>
                  {{
                    carregandoFila
                      ? '...'
                      : numero(totalFila)
                  }}
                </strong>
              </div>

              <p
                v-if="erroFila"
                class="alert error"
                role="alert"
              >
                {{ erroFila }}
              </p>

              <div
                v-else-if="
                  !carregandoFila &&
                  pendentes.length === 0
                "
                class="empty-queue"
              >
                Nenhum CRMV pendente no momento.
              </div>

              <div
                v-else
                class="pending-list"
              >
                <div
                  v-for="vet in pendentes.slice(0, 3)"
                  :key="vet.id"
                  class="pending-row"
                >
                  <span
                    class="vet-symbol"
                    aria-hidden="true"
                  >
                    ♙
                  </span>

                  <div class="vet-details">
                    <strong>
                      {{ vet.nome }}
                    </strong>

                    <small>
                      CRMV: {{ vet.crmv }}
                    </small>
                  </div>

                  <span class="tag-pending">
                    Pendente
                  </span>
                </div>
              </div>

              <button
                class="text-action"
                type="button"
                @click="abrirFilaCompleta"
              >
                Ver fila completa →
              </button>
            </div>

            <div
              v-else
              class="investor-info"
            >
              <strong>
                {{
                  carregando
                    ? '...'
                    : numero(resumo?.crmv_pendentes)
                }}
                pendentes
              </strong>

              <p>
                Informações individuais e decisões
                de CRMV são restritas aos administradores.
              </p>
            </div>
          </section>
        </div>

        <!-- FILA COMPLETA -->
        <section
          v-if="ehAdministrador && mostrarFilaCompleta"
          id="fila-completa"
          class="panel full-queue"
        >
          <div class="table-heading">
            <div>
              <h3>
                Veterinários aguardando análise
              </h3>

              <p>
                Confira a fonte oficial antes de
                aprovar um CRMV.
              </p>
            </div>

            <button
              type="button"
              class="outline-button"
              :disabled="carregandoFila"
              @click="carregarFila(paginaFila)"
            >
              Atualizar fila
            </button>
          </div>

          <p
            v-if="erroFila"
            class="alert error"
            role="alert"
          >
            {{ erroFila }}
          </p>

          <div
            v-else-if="
              !carregandoFila &&
              pendentes.length === 0
            "
            class="empty-queue"
          >
            Nenhum registro nesta página.
          </div>

          <div
            v-else
            class="table-scroll"
          >
            <table>
              <thead>
                <tr>
                  <th>Veterinário</th>
                  <th>CRMV</th>
                  <th>Cidade</th>
                  <th>Cadastro</th>
                  <th>Status</th>
                  <th>Ações</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="vet in pendentes"
                  :key="vet.id"
                >
                  <td>
                    <strong>
                      {{ vet.nome }}
                    </strong>
                  </td>

                  <td>
                    {{ vet.crmv }}

                    <small v-if="vet.crmv_uf">
                      / {{ vet.crmv_uf }}
                    </small>
                  </td>

                  <td>
                    {{ vet.cidade || '—' }}
                    /
                    {{ vet.uf || '—' }}
                  </td>

                  <td>
                    {{ dataCadastro(vet.created_at) }}
                  </td>

                  <td>
                    <span class="tag-pending">
                      Pendente
                    </span>
                  </td>

                  <td class="decision-buttons">
                    <button
                      type="button"
                      class="approve-button"
                      @click="abrirDecisao(vet, 'aprovar')"
                    >
                      Aprovar
                    </button>

                    <button
                      type="button"
                      class="reject-button"
                      @click="abrirDecisao(vet, 'rejeitar')"
                    >
                      Rejeitar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- PAGINAÇÃO -->
          <div class="pagination">
            <span>
              Página {{ paginaFila }}
              de {{ ultimaPaginaFila }}
              · {{ numero(totalFila) }} pendentes
            </span>

            <div>
              <button
                class="outline-button"
                type="button"
                :disabled="
                  carregandoFila ||
                  paginaFila <= 1
                "
                @click="
                  carregarFila(paginaFila - 1)
                "
              >
                Anterior
              </button>

              <button
                class="outline-button"
                type="button"
                :disabled="
                  carregandoFila ||
                  paginaFila >= ultimaPaginaFila
                "
                @click="
                  carregarFila(paginaFila + 1)
                "
              >
                Próxima
              </button>
            </div>
          </div>
        </section>
      </main>
    </div>

    <!-- MODAL DE DECISÃO -->
    <div
      v-if="modalAberto && veterinarioSelecionado"
      class="modal-backdrop"
      @click.self="fecharDecisao"
    >
      <section
        class="modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title"
        @keydown.esc="fecharDecisao"
      >
        <div class="modal-heading">
          <div>
            <p class="modal-caption">
              Verificação administrativa
            </p>

            <h2 id="modal-title">
              {{
                tipoDecisao === 'aprovar'
                  ? 'Aprovar CRMV'
                  : 'Rejeitar CRMV'
              }}
            </h2>
          </div>

          <button
            type="button"
            class="close-button"
            aria-label="Fechar"
            :disabled="salvandoDecisao"
            @click="fecharDecisao"
          >
            ×
          </button>
        </div>

        <p class="modal-vet">
          <strong>
            {{ veterinarioSelecionado.nome }}
          </strong>

          <span>
            CRMV informado:
            {{ veterinarioSelecionado.crmv }}
          </span>
        </p>

        <form @submit.prevent="confirmarDecisao">

          <!-- APROVAR -->
          <template v-if="tipoDecisao === 'aprovar'">
            <p class="form-notice">
              Consulte o registro e a situação
              profissional no conselho oficial
              antes de confirmar.

              O sistema registra sua declaração;
              não verifica o CRMV automaticamente.
            </p>

            <label for="crmv-uf">
              UF do registro CRMV
            </label>

            <input
              id="crmv-uf"
              v-model.trim="ufCrmv"
              maxlength="2"
              placeholder="Ex.: MG"
              required
              autocomplete="off"
            />

            <label for="crmv-fonte">
              Fonte oficial consultada
            </label>

            <input
              id="crmv-fonte"
              v-model.trim="fonte"
              maxlength="255"
              placeholder="Ex.: CRMV-MG / consulta cadastral"
              required
            />

            <label class="check-label">
              <input
                v-model="consultaConfirmada"
                type="checkbox"
                required
              />

              Confirmo que realizei a consulta
              oficial e conferi os dados.
            </label>
          </template>

          <!-- REJEITAR -->
          <template v-else>
            <label for="motivo-rejeicao">
              Justificativa da rejeição
            </label>

            <textarea
              id="motivo-rejeicao"
              v-model.trim="motivoRejeicao"
              rows="4"
              minlength="10"
              maxlength="1000"
              required
              placeholder="Descreva o motivo da rejeição (mínimo 10 caracteres)."
            ></textarea>

            <label for="fonte-rejeicao">
              Fonte consultada (opcional)
            </label>

            <input
              id="fonte-rejeicao"
              v-model.trim="fonte"
              maxlength="255"
              placeholder="Ex.: CRMV-MG"
            />
          </template>

          <p
            v-if="erroDecisao"
            class="alert error"
            role="alert"
          >
            {{ erroDecisao }}
          </p>

          <div class="modal-actions">
            <button
              type="button"
              class="outline-button"
              :disabled="salvandoDecisao"
              @click="fecharDecisao"
            >
              Cancelar
            </button>

            <button
              type="submit"
              :class="
                tipoDecisao === 'aprovar'
                  ? 'submit-approve'
                  : 'submit-reject'
              "
              :disabled="salvandoDecisao"
            >
              {{
                salvandoDecisao
                  ? 'Salvando...'
                  : (
                      tipoDecisao === 'aprovar'
                        ? 'Confirmar aprovação'
                        : 'Confirmar rejeição'
                    )
              }}
            </button>
          </div>
        </form>
      </section>
    </div>
  </div>
</template>

<style scoped>
.admin-dashboard {
  min-height: 100vh;
  background: #f7f9f8;
  color: #1f4033;
  font-family: inherit;
}

/* CABEÇALHO */
.topbar {
  min-height: 70px;
  background: #124d38;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 12px 25px;
}

.brand,
.top-actions,
.user-pill {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand strong {
  font-size: 18px;
  white-space: nowrap;
}

.brand-paw {
  font-size: 24px;
}

.brand-separator {
  width: 1px;
  height: 25px;
  background: #ffffff55;
}

.brand-description {
  font-size: 12px;
  color: #d2e9db;
}

.search-upcoming {
  color: #cce7d7;
  font-size: 12px;
}

.top-actions {
  gap: 15px;
}

.avatar {
  display: grid;
  place-items: center;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #e3f6e9;
  color: #175a3a;
  font-weight: 700;
}

.username {
  font-size: 13px;
}

.exit-button {
  background: transparent;
  color: white;
  border: 1px solid #ffffff77;
  border-radius: 8px;
  padding: 9px 14px;
  cursor: pointer;
}

.exit-button:hover {
  background: #ffffff22;
}

.exit-button:disabled {
  opacity: 0.6;
}

/* ESTRUTURA */
.app-layout {
  display: grid;
  grid-template-columns: 205px minmax(0, 1fr);
  min-height: calc(100vh - 70px);
}

.sidebar {
  background: white;
  border-right: 1px solid #e6eee9;
  padding: 23px 12px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.side-links {
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.side-link {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 43px;
  padding: 9px 11px;
  border-radius: 9px;
  color: #375b47;
  text-decoration: none;
  font-size: 13px;
}

.side-link > span:first-child {
  font-size: 20px;
  width: 22px;
  text-align: center;
}

.side-link.selected {
  background: #dff2e5;
  color: #12583c;
  font-weight: 700;
}

.side-link:not(.disabled):hover {
  background: #edf7f0;
}

.side-link.disabled {
  color: #87978b;
}

.side-link small {
  margin-left: auto;
  font-size: 10px;
  color: #a5b1a9;
}

.side-separator {
  height: 1px;
  background: #e5eee7;
  margin: 13px 0;
}

.side-footer {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 7px;
  font-size: 27px;
  color: #b0cbb8;
}

.side-footer small {
  font-size: 11px;
}

/* CONTEÚDO */
.content {
  width: 100%;
  max-width: 1620px;
  margin: auto;
  padding: 31px 30px;
  min-width: 0;
}

.page-heading {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  margin-bottom: 22px;
}

.page-heading h1 {
  font-size: clamp(23px, 2.3vw, 31px);
  line-height: 1.25;
  color: #173e2c;
  margin: 0 0 19px;
}

.intro {
  border-left: 4px solid #21a15e;
  padding-left: 14px;
}

.intro h2 {
  margin: 0 0 3px;
  font-size: 17px;
  color: #186945;
}

.intro p {
  margin: 0;
  font-size: 12px;
  color: #73867a;
}

.outline-button {
  border: 1px solid #d4e4d9;
  background: #fff;
  border-radius: 9px;
  color: #1d6845;
  padding: 10px 12px;
  font-size: 12px;
  white-space: nowrap;
  cursor: pointer;
}

.outline-button:hover {
  background: #edf7ef;
}

.outline-button:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

/* INDICADORES */
.primary-metrics {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 13px;
  margin-bottom: 12px;
}

.metric-card {
  background: white;
  border: 1px solid #e2eae4;
  box-shadow: 0 3px 11px #123d3010;
  border-radius: 13px;
  min-width: 0;
  padding: 17px 15px;
  display: flex;
  align-items: center;
  gap: 13px;
}

.metric-symbol {
  display: grid;
  place-items: center;
  background: #e1f4e8;
  border-radius: 10px;
  color: #1e8554;
  flex-shrink: 0;
  width: 43px;
  height: 43px;
  font-size: 23px;
}

.metric-text {
  min-width: 0;
}

.metric-text p,
.compact-metric p {
  margin: 0 0 5px;
  font-size: 11px;
  line-height: 1.25;
  color: #566f60;
}

.metric-text strong {
  font-size: 26px;
  color: #153f2c;
  line-height: 1.1;
}

.secondary-metrics {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 13px;
  margin-bottom: 21px;
}

.compact-metric {
  background: #edf7f0;
  border: 1px solid #deebe1;
  border-radius: 11px;
  padding: 13px 17px;
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.compact-symbol {
  width: 35px;
  height: 35px;
  display: grid;
  place-items: center;
  border-radius: 9px;
  background: white;
  color: #258053;
  font-size: 18px;
  flex-shrink: 0;
}

.compact-metric strong {
  font-size: 21px;
  color: #185a3a;
}

/* GRÁFICO E CRMV */
.content-columns {
  display: grid;
  grid-template-columns: 1.12fr 1fr;
  gap: 16px;
  margin-bottom: 17px;
  align-items: stretch;
}

.panel {
  background: white;
  border: 1px solid #e0e8e2;
  border-radius: 13px;
  box-shadow: 0 4px 12px #123d300b;
  min-width: 0;
  padding: 20px;
}

.panel-heading,
.table-heading {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: flex-start;
}

.panel h3,
.table-heading h3 {
  margin: 0 0 5px;
  font-size: 17px;
  color: #1c4030;
}

.panel-heading p,
.table-heading p {
  margin: 0;
  font-size: 12px;
  color: #78887d;
}

/* CRMV */
.crmv-panel {
  background: linear-gradient(#f1faf4, #fff 48%);
}

.shield-mark {
  color: #248454;
  background: #dcf3e5;
  border-radius: 10px;
  padding: 8px 12px;
  font-size: 21px;
}

.queue-counter {
  margin-top: 17px;
  margin-bottom: 8px;
  background: #e8f4eb;
  padding: 11px 13px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 9px;
}

.queue-counter span {
  color: #496c56;
  font-size: 12px;
}

.queue-counter strong {
  font-size: 23px;
  color: #176943;
}

.pending-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 0;
  border-bottom: 1px solid #e5eee8;
}

.vet-symbol {
  background: #e0f3e7;
  color: #247a50;
  border-radius: 50%;
  display: grid;
  place-items: center;
  flex-shrink: 0;
  width: 34px;
  height: 34px;
  font-size: 20px;
}

.vet-details {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.vet-details strong {
  font-size: 12px;
  color: #1d4630;
  overflow-wrap: anywhere;
}

.vet-details small {
  font-size: 11px;
  color: #788b7e;
}

.tag-pending {
  background: #fff1d5;
  color: #9b690f;
  border-radius: 25px;
  padding: 6px 8px;
  font-size: 10px;
  white-space: nowrap;
}

.text-action {
  border: 0;
  background: none;
  color: #167a4b;
  cursor: pointer;
  margin-top: 13px;
  padding: 5px 0;
  font-size: 13px;
  font-weight: 700;
}

.text-action:hover {
  text-decoration: underline;
}

.empty-queue,
.investor-info {
  padding: 18px 0;
  font-size: 13px;
  color: #718777;
}

.investor-info strong {
  font-size: 24px;
  color: #195f3d;
}

.investor-info p {
  line-height: 1.5;
}

/* TABELA DE CRMV */
.alert {
  border-radius: 9px;
  padding: 11px 14px;
  font-size: 13px;
  margin: 0 0 15px;
}

.alert.error {
  background: #fff1f1;
  color: #922c2c;
}

.alert.success {
  background: #e6f5e9;
  color: #1a6d3c;
}

.full-queue {
  scroll-margin-top: 15px;
}

.table-heading {
  margin-bottom: 17px;
}

.table-scroll {
  overflow-x: auto;
}

table {
  border-collapse: collapse;
  width: 100%;
  min-width: 720px;
}

th,
td {
  text-align: left;
  padding: 12px 11px;
  border-bottom: 1px solid #eaf0eb;
  font-size: 12px;
}

th {
  background: #ebf5ed;
  color: #355d43;
  font-weight: 700;
}

td {
  color: #3f5849;
}

td strong {
  color: #224a35;
}

td small {
  color: #839287;
}

.decision-buttons {
  white-space: nowrap;
}

.approve-button,
.reject-button,
.submit-approve,
.submit-reject {
  border: 0;
  border-radius: 7px;
  padding: 9px 12px;
  color: #fff;
  cursor: pointer;
  font-weight: 700;
  font-size: 12px;
}

.approve-button,
.submit-approve {
  background: #176d45;
}

.reject-button,
.submit-reject {
  background: #a64141;
}

.reject-button {
  margin-left: 7px;
}

.approve-button:hover,
.submit-approve:hover {
  background: #0f5232;
}

.reject-button:hover,
.submit-reject:hover {
  background: #873434;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  margin-top: 16px;
  color: #6b8171;
  font-size: 12px;
}

.pagination > div {
  display: flex;
  gap: 8px;
}

/* MODAL */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: #0c2319ad;
  display: grid;
  place-items: center;
  padding: 16px;
  z-index: 100;
}

.modal {
  width: min(100%, 530px);
  max-height: 95vh;
  overflow: auto;
  border-radius: 15px;
  background: #fff;
  padding: 25px;
  box-shadow: 0 20px 90px #0b211b4a;
}

.modal-heading {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.modal-caption {
  margin: 0 0 5px;
  color: #25804e;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.modal h2 {
  font-size: 23px;
  margin: 0 0 14px;
  color: #153f2e;
}

.close-button {
  border: 0;
  background: #eef4ef;
  border-radius: 8px;
  font-size: 24px;
  width: 35px;
  height: 35px;
  cursor: pointer;
  color: #2b5f43;
}

.modal-vet {
  background: #f2f8f4;
  border-radius: 9px;
  padding: 12px 14px;
  margin: 0 0 20px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.modal-vet strong {
  font-size: 14px;
}

.modal-vet span {
  font-size: 12px;
  color: #63826d;
}

.modal form {
  display: flex;
  flex-direction: column;
  gap: 9px;
}

.modal form > label {
  font-size: 12px;
  font-weight: 700;
  color: #315943;
}

.modal input:not([type=checkbox]),
.modal textarea {
  font: inherit;
  font-size: 14px;
  border: 1px solid #cbded1;
  border-radius: 8px;
  padding: 11px;
  outline-color: #2b9962;
  width: 100%;
  box-sizing: border-box;
}

.form-notice {
  font-size: 12px;
  line-height: 1.5;
  color: #5c7565;
  margin: 0 0 8px;
}

.check-label {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin: 9px 0 6px;
  line-height: 1.5;
}

.check-label input {
  accent-color: #198253;
  margin-top: 3px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 15px;
}

.modal-actions button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* RESPONSIVIDADE */
@media (max-width: 1100px) {
  .content {
    padding: 24px 18px;
  }

  .metric-card {
    padding: 14px 11px;
    gap: 9px;
    flex-direction: column;
    align-items: flex-start;
  }

  .metric-symbol {
    width: 35px;
    height: 35px;
    font-size: 19px;
  }

  .metric-text strong {
    font-size: 23px;
  }

  .content-columns {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 800px) {
  .primary-metrics {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .secondary-metrics {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .brand-description,
  .brand-separator,
  .search-upcoming {
    display: none;
  }

  .app-layout {
    grid-template-columns: 1fr;
  }

  .sidebar {
    border-right: 0;
    border-bottom: 1px solid #e5eee9;
    padding: 8px 15px;
  }

  .side-links {
    flex-direction: row;
    flex-wrap: wrap;
  }

  .side-link.disabled,
  .side-separator,
  .side-footer {
    display: none;
  }

  .topbar {
    padding: 12px 15px;
  }
}

@media (max-width: 560px) {
  .page-heading {
    flex-direction: column;
  }

  .secondary-metrics {
    grid-template-columns: 1fr;
  }

  .primary-metrics {
    gap: 8px;
  }

  .metric-card {
    padding: 13px;
  }

  .metric-text strong {
    font-size: 23px;
  }

  .username {
    display: none;
  }

  .pagination {
    flex-direction: column;
    align-items: flex-start;
  }

  .top-actions {
    gap: 7px;
  }

  .brand strong {
    font-size: 15px;
  }

  .topbar {
    gap: 8px;
  }

  .modal {
    padding: 17px;
  }
}
</style>

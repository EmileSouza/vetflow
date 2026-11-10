<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import adminApi, {
  obterAdmin,
  logoutAdmin
} from '../services/adminApi'

const router = useRouter()

const usuario = ref(null)
const verificandoAcesso = ref(true)
const carregando = ref(false)
const carregandoDetalhe = ref(false)
const saindo = ref(false)

const erro = ref('')
const erroDetalhe = ref('')
const erroFiltro = ref('')

// Filtros
const busca = ref('')
const statusFiltro = ref('')
const ufFiltro = ref('')

// Listagem
const veterinarios = ref([])
const paginaAtual = ref(1)
const ultimaPagina = ref(1)
const total = ref(0)

// Detalhes selecionados
const selecionado = ref(null)
let consultaDetalhesAtual = 0

const haFiltros = computed(() =>
  busca.value.trim() !== '' ||
  statusFiltro.value !== '' ||
  ufFiltro.value.trim() !== ''
)

function numero(valor) {
  return new Intl.NumberFormat('pt-BR').format(
    Number(valor) || 0
  )
}

function dataFormatada(valor) {
  if (!valor) return '—'

  const data = new Date(valor)

  return Number.isNaN(data.getTime())
    ? '—'
    : new Intl.DateTimeFormat('pt-BR', {
        dateStyle: 'short',
        timeStyle: 'short'
      }).format(data)
}

function tituloStatus(status) {
  return {
    pendente: 'Pendente',
    aprovado: 'Aprovado',
    rejeitado: 'Rejeitado'
  }[status] || 'Não informado'
}

function mensagemErro(error, padrao) {
  const status = error?.response?.status

  if (status === 419 || status === 401) {
    return 'Sua sessão expirou. Entre novamente.'
  }

  if (status === 403) {
    return 'Sua conta não possui permissão para acessar estes dados.'
  }

  if (status === 422) {
    return 'Confira os filtros informados.'
  }

  return error?.response?.data?.message || padrao
}

// Verifica a sessão administrativa.
async function verificarAcesso() {
  try {
    const resposta = await obterAdmin()

    usuario.value = resposta.data

    // Investidores não consultam dados individuais.
    if (usuario.value?.papel !== 'admin') {
      await router.replace({
        name: 'AdminDashboard'
      })
      return
    }

    await buscarVeterinarios(1)

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
      'Não foi possível verificar seu acesso.'
    )
  } finally {
    verificandoAcesso.value = false
  }
}

// Consulta a API de veterinários.
async function buscarVeterinarios(pagina = 1) {
  if (carregando.value) return

  const uf = ufFiltro.value.trim().toUpperCase()

  if (uf && !/^[A-Z]{2}$/.test(uf)) {
    erroFiltro.value =
      'Informe uma UF com duas letras, como MG ou SP.'
    return
  }

  erroFiltro.value = ''
  erro.value = ''
  carregando.value = true

  const params = {
    page: pagina
  }

  if (busca.value.trim()) {
    params.busca = busca.value.trim()
  }

  if (statusFiltro.value) {
    params.status_crmv = statusFiltro.value
  }

  if (uf) {
    params.uf = uf
  }

  try {
    const resposta = await adminApi.get(
      '/admin/veterinarios',
      { params }
    )

    veterinarios.value = resposta.data.data ?? []

    paginaAtual.value =
      resposta.data.meta?.pagina_atual ?? 1

    ultimaPagina.value =
      resposta.data.meta?.ultima_pagina ?? 1

    total.value =
      resposta.data.meta?.total ?? 0

    // Evita exibir detalhes de uma pesquisa anterior.
    consultaDetalhesAtual++
    selecionado.value = null
    erroDetalhe.value = ''
    carregandoDetalhe.value = false

  } catch (error) {
    erro.value = mensagemErro(
      error,
      'Não foi possível carregar os veterinários.'
    )

    if (
      [401, 419].includes(
        error?.response?.status
      )
    ) {
      await router.replace({
        name: 'AdminLogin'
      })
    }
  } finally {
    carregando.value = false
  }
}

function aplicarFiltros() {
  buscarVeterinarios(1)
}

function limparFiltros() {
  busca.value = ''
  statusFiltro.value = ''
  ufFiltro.value = ''

  buscarVeterinarios(1)
}

// Consulta detalhes individuais.
async function abrirDetalhes(vet) {
  const numeroConsulta = ++consultaDetalhesAtual

  carregandoDetalhe.value = true
  erroDetalhe.value = ''
  selecionado.value = null

  try {
    const resposta = await adminApi.get(
      `/admin/veterinarios/${vet.id}`
    )

    if (numeroConsulta === consultaDetalhesAtual) {
      selecionado.value = resposta.data.data
    }

  } catch (error) {
    if (numeroConsulta === consultaDetalhesAtual) {
      erroDetalhe.value = mensagemErro(
        error,
        'Não foi possível carregar os detalhes.'
      )
    }

    if (
      [401, 419].includes(
        error?.response?.status
      )
    ) {
      await router.replace({
        name: 'AdminLogin'
      })
    }

  } finally {
    if (numeroConsulta === consultaDetalhesAtual) {
      carregandoDetalhe.value = false
    }
  }
}

// Encerrar sessão.
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

onMounted(verificarAcesso)
</script>

<template>
  <div class="pagina-admin">

    <!-- CABEÇALHO -->
    <header class="cabecalho">
      <div class="marca">
        <span aria-hidden="true">🐾</span>
        <strong>VetFlow Admin</strong>
        <small>Plataforma Veterinária</small>
      </div>

      <div class="cabecalho-acoes">
        <span v-if="usuario" class="usuario">
          {{ usuario.nome }}
        </span>

        <button
          type="button"
          class="botao-sair"
          :disabled="saindo"
          @click="sair"
        >
          {{ saindo ? 'Saindo...' : 'Sair' }}
        </button>
      </div>
    </header>

    <div class="estrutura">

      <!-- MENU LATERAL -->
      <aside class="menu-lateral">
        <nav
          aria-label="Menu administrativo"
          class="navegacao"
        >
          <RouterLink
            :to="{ name: 'AdminDashboard' }"
            class="menu-item"
          >
            <span aria-hidden="true">⌂</span>
            Dashboard
          </RouterLink>

          <RouterLink
            :to="{ name: 'AdminVeterinarios' }"
            class="menu-item selecionado"
          >
            <span aria-hidden="true">♙</span>
            Veterinários
          </RouterLink>

          <RouterLink
            :to="{ name: 'AdminDashboard' }"
            class="menu-item"
          >
            <span aria-hidden="true">✓</span>
            CRMV
            <small>No Dashboard</small>
          </RouterLink>

          <span class="menu-item indisponivel">
            <span aria-hidden="true">▦</span>
            Agendamentos
            <small>Em breve</small>
          </span>

          <span class="menu-item indisponivel">
            <span aria-hidden="true">▥</span>
            Relatórios
            <small>Em breve</small>
          </span>

          <span class="menu-item indisponivel">
            <span aria-hidden="true">⚙</span>
            Configurações
            <small>Em breve</small>
          </span>
        </nav>

        <small class="rodape">
          VetFlow © 2026
        </small>
      </aside>

      <!-- CONTEÚDO -->
      <main class="conteudo">
        <div class="titulo-pagina">
          <div>
            <h1>Gestão de Veterinários</h1>

            <div class="descricao">
              <h2>Profissionais cadastrados</h2>

              <p>
                Consulte registros, situação do CRMV
                e informações de verificação.
              </p>
            </div>
          </div>

          <button
            type="button"
            class="botao-claro"
            :disabled="carregando || verificandoAcesso"
            @click="buscarVeterinarios(paginaAtual)"
          >
            Atualizar lista
          </button>
        </div>

        <p
          v-if="erro"
          class="alerta erro"
          role="alert"
        >
          {{ erro }}
        </p>

        <p
          v-if="verificandoAcesso"
          role="status"
          class="mensagem"
        >
          Verificando acesso administrativo...
        </p>

        <!-- CONTEÚDO RESTRITO AO ADMINISTRADOR -->
        <template v-else-if="usuario?.papel === 'admin'">

          <div class="corpo-colunas">

            <!-- LISTA -->
            <section
              class="painel lista-painel"
              aria-labelledby="titulo-lista"
            >
              <div class="painel-titulo">
                <div>
                  <h3 id="titulo-lista">
                    Lista de veterinários
                  </h3>

                  <p>
                    Exibindo dados reais da plataforma,
                    com paginação de dez registros.
                  </p>
                </div>

                <span class="total">
                  {{ numero(total) }} registros
                </span>
              </div>

              <!-- FILTROS -->
              <form
                class="filtros"
                @submit.prevent="aplicarFiltros"
              >
                <div class="campo busca">
                  <label for="busca-veterinario">
                    Pesquisar por nome ou CRMV
                  </label>

                  <input
                    id="busca-veterinario"
                    v-model="busca"
                    type="search"
                    maxlength="100"
                    placeholder="Ex.: Ana ou CRMV-MG..."
                  />
                </div>

                <div class="campo">
                  <label for="filtro-status">
                    Status do CRMV
                  </label>

                  <select
                    id="filtro-status"
                    v-model="statusFiltro"
                  >
                    <option value="">Todos</option>
                    <option value="pendente">Pendente</option>
                    <option value="aprovado">Aprovado</option>
                    <option value="rejeitado">Rejeitado</option>
                  </select>
                </div>

                <div class="campo campo-uf">
                  <label for="filtro-uf">
                    UF da localização
                  </label>

                  <input
                    id="filtro-uf"
                    v-model.trim="ufFiltro"
                    type="text"
                    maxlength="2"
                    placeholder="MG"
                    autocomplete="off"
                  />
                </div>

                <div class="acoes-filtro">
                  <button
                    class="botao-verde"
                    type="submit"
                    :disabled="carregando"
                  >
                    {{
                      carregando
                        ? 'Buscando...'
                        : 'Buscar'
                    }}
                  </button>

                  <button
                    class="botao-claro"
                    type="button"
                    :disabled="carregando || !haFiltros"
                    @click="limparFiltros"
                  >
                    Limpar
                  </button>
                </div>
              </form>

              <p
                v-if="erroFiltro"
                class="alerta erro"
                role="alert"
              >
                {{ erroFiltro }}
              </p>

              <!-- ESTADOS DA LISTA -->
              <div
                v-if="carregando"
                class="mensagem"
                role="status"
              >
                Carregando veterinários...
              </div>

              <div
                v-else-if="!erro && veterinarios.length === 0"
                class="mensagem"
              >
                Nenhum veterinário encontrado
                com esses filtros.
              </div>

              <!-- TABELA -->
              <div v-else class="tabela-rolagem">
                <table>
                  <thead>
                    <tr>
                      <th>Veterinário</th>
                      <th>CRMV</th>
                      <th>Localização</th>
                      <th>Status CRMV</th>
                      <th>Conta</th>
                      <th>Ação</th>
                    </tr>
                  </thead>

                  <tbody>
                    <tr
                      v-for="vet in veterinarios"
                      :key="vet.id"
                      :class="{
                        'linha-selecionada':
                          selecionado?.id === vet.id
                      }"
                    >
                      <td>
                        <strong>{{ vet.nome }}</strong>
                        <small>
                          {{
                            vet.especialidade ||
                            'Especialidade não informada'
                          }}
                        </small>
                      </td>

                      <td>
                        {{ vet.crmv }}

                        <small v-if="vet.crmv_uf">
                          CRMV-{{ vet.crmv_uf }}
                        </small>
                      </td>

                      <td>
                        {{ vet.cidade || '—' }}

                        <small>
                          {{
                            vet.uf ||
                            'UF não informada'
                          }}
                        </small>
                      </td>

                      <td>
                        <span
                          class="status"
                          :class="`status-${vet.status_crmv}`"
                        >
                          {{ tituloStatus(vet.status_crmv) }}
                        </span>
                      </td>

                      <td>
                        <span
                          :class="
                            vet.ativo
                              ? 'conta-ativa'
                              : 'conta-inativa'
                          "
                        >
                          {{
                            vet.ativo
                              ? 'Ativa'
                              : 'Inativa'
                          }}
                        </span>
                      </td>

                      <td>
                        <button
                          type="button"
                          class="botao-detalhe"
                          :disabled="carregandoDetalhe"
                          @click="abrirDetalhes(vet)"
                        >
                          Detalhes
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- PAGINAÇÃO -->
              <div class="paginacao">
                <span>
                  Página {{ paginaAtual }}
                  de {{ ultimaPagina }}
                  · {{ numero(total) }} registros
                </span>

                <div>
                  <button
                    class="botao-claro"
                    type="button"
                    :disabled="
                      carregando ||
                      paginaAtual <= 1
                    "
                    @click="
                      buscarVeterinarios(paginaAtual - 1)
                    "
                  >
                    Anterior
                  </button>

                  <button
                    class="botao-claro"
                    type="button"
                    :disabled="
                      carregando ||
                      paginaAtual >= ultimaPagina
                    "
                    @click="
                      buscarVeterinarios(paginaAtual + 1)
                    "
                  >
                    Próxima
                  </button>
                </div>
              </div>
            </section>

            <!-- PAINEL DE DETALHES -->
            <aside
              class="painel detalhe-painel"
              aria-labelledby="titulo-detalhes"
            >
              <div class="painel-titulo">
                <div>
                  <h3 id="titulo-detalhes">
                    Detalhes do veterinário
                  </h3>

                  <p>
                    Registro e auditoria da verificação de CRMV.
                  </p>
                </div>
              </div>

              <p
                v-if="carregandoDetalhe"
                class="mensagem"
                role="status"
              >
                Carregando detalhes...
              </p>

              <p
                v-else-if="erroDetalhe"
                class="alerta erro"
                role="alert"
              >
                {{ erroDetalhe }}
              </p>

              <p
                v-else-if="!selecionado"
                class="mensagem"
              >
                Selecione um veterinário na tabela
                para ver os detalhes.
              </p>

              <template v-else>
                <div class="perfil-selecionado">
                  <div
                    class="avatar-vet"
                    aria-hidden="true"
                  >
                    ♙
                  </div>

                  <div>
                    <strong>
                      {{ selecionado.nome }}
                    </strong>

                    <span>
                      {{
                        selecionado.especialidade ||
                        'Especialidade não informada'
                      }}
                    </span>
                  </div>
                </div>

                <div class="linha-detalhe">
                  <span>CRMV</span>
                  <strong>{{ selecionado.crmv }}</strong>
                </div>

                <div class="linha-detalhe">
                  <span>UF do CRMV</span>
                  <strong>
                    {{
                      selecionado.crmv_uf ||
                      'Não informada'
                    }}
                  </strong>
                </div>

                <div class="linha-detalhe">
                  <span>Localização</span>
                  <strong>
                    {{ selecionado.cidade || '—' }}
                    /
                    {{ selecionado.uf || '—' }}
                  </strong>
                </div>

                <div class="linha-detalhe">
                  <span>Status CRMV</span>

                  <span
                    class="status"
                    :class="
                      `status-${selecionado.status_crmv}`
                    "
                  >
                    {{
                      tituloStatus(
                        selecionado.status_crmv
                      )
                    }}
                  </span>
                </div>

                <div class="linha-detalhe">
                  <span>Conta ativa</span>
                  <strong>
                    {{
                      selecionado.ativo
                        ? 'Sim'
                        : 'Não'
                    }}
                  </strong>
                </div>

                <div class="linha-detalhe">
                  <span>Cadastrado em</span>
                  <strong>
                    {{
                      dataFormatada(
                        selecionado.created_at
                      )
                    }}
                  </strong>
                </div>

                <div class="linha-detalhe">
                  <span>Verificado em</span>
                  <strong>
                    {{
                      dataFormatada(
                        selecionado.crmv_verificado_em
                      )
                    }}
                  </strong>
                </div>

                <div class="linha-detalhe">
                  <span>Verificado por</span>
                  <strong>
                    {{
                      selecionado.verificador_nome ||
                      '—'
                    }}
                  </strong>
                </div>

                <div class="linha-detalhe">
                  <span>Método</span>
                  <strong>
                    {{
                      selecionado.metodo_verificacao ||
                      '—'
                    }}
                  </strong>
                </div>

                <div class="detalhe-bloco">
                  <span>Fonte consultada</span>

                  <p>
                    {{
                      selecionado.fonte_verificacao ||
                      'Não informada.'
                    }}
                  </p>
                </div>

                <div
                  v-if="
                    selecionado.status_crmv === 'rejeitado'
                  "
                  class="detalhe-bloco bloco-rejeicao"
                >
                  <span>
                    Justificativa da rejeição
                  </span>

                  <p>
                    {{
                      selecionado.motivo_rejeicao_crmv ||
                      'Não informada.'
                    }}
                  </p>
                </div>

                <div
                  v-if="
                    selecionado.status_crmv === 'pendente'
                  "
                  class="aviso-pendente"
                >
                  <strong>
                    CRMV aguardando análise
                  </strong>

                  <p>
                    As decisões de aprovação e rejeição
                    continuam na fila do Dashboard.
                  </p>

                  <RouterLink
                    :to="{ name: 'AdminDashboard' }"
                    class="botao-verde link-acao"
                  >
                    Ir para o Dashboard
                  </RouterLink>
                </div>
              </template>
            </aside>
          </div>
        </template>
      </main>
    </div>
  </div>
</template>

<style scoped>
.pagina-admin {
  min-height: 100vh;
  background: #f6f8f7;
  color: #203c2f;
  font-family: inherit;
}

.cabecalho {
  min-height: 70px;
  padding: 14px 26px;
  background: #124d38;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.marca,
.cabecalho-acoes {
  display: flex;
  align-items: center;
  gap: 13px;
}

.marca > span {
  font-size: 22px;
}

.marca strong {
  font-size: 19px;
}

.marca small {
  color: #cce5d5;
  font-size: 12px;
  border-left: 1px solid #ffffff55;
  padding-left: 13px;
}

.usuario {
  font-size: 13px;
}

.botao-sair {
  border: 1px solid #ffffff88;
  background: transparent;
  border-radius: 8px;
  color: #fff;
  padding: 9px 14px;
  cursor: pointer;
}

.estrutura {
  display: grid;
  grid-template-columns: 210px minmax(0, 1fr);
  min-height: calc(100vh - 70px);
}

.menu-lateral {
  background: #fff;
  border-right: 1px solid #e4ebe6;
  padding: 25px 13px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.navegacao {
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 9px;
  text-decoration: none;
  font-size: 13px;
  color: #3a5848;
}

.menu-item > span:first-child {
  font-size: 19px;
  width: 22px;
}

.menu-item.selecionado {
  background: #dff2e5;
  color: #12583c;
  font-weight: 700;
}

.menu-item:not(.indisponivel):hover {
  background: #eaf6ee;
}

.menu-item.indisponivel {
  color: #89998d;
}

.menu-item small {
  font-size: 10px;
  color: #90a79a;
  margin-left: auto;
}

.rodape {
  text-align: center;
  color: #91ab9b;
}

.conteudo {
  width: 100%;
  max-width: 1640px;
  margin: 0 auto;
  padding: 32px;
  min-width: 0;
}

.titulo-pagina {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 18px;
  margin-bottom: 23px;
}

.titulo-pagina h1 {
  font-size: clamp(23px, 2.5vw, 31px);
  color: #17432f;
  margin: 0 0 19px;
}

.descricao {
  border-left: 4px solid #21a15e;
  padding-left: 14px;
}

.descricao h2 {
  margin: 0 0 5px;
  color: #186945;
  font-size: 17px;
}

.descricao p,
.painel-titulo p {
  margin: 0;
  color: #72877a;
  font-size: 12px;
}

.corpo-colunas {
  display: grid;
  grid-template-columns: minmax(0, 1.6fr) minmax(300px, 0.9fr);
  gap: 17px;
  align-items: start;
}

.painel {
  min-width: 0;
  background: #fff;
  border: 1px solid #e1e8e2;
  box-shadow: 0 4px 14px #124d3810;
  border-radius: 14px;
  padding: 21px;
}

.painel-titulo {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 19px;
}

.painel-titulo h3 {
  font-size: 18px;
  color: #1b422d;
  margin: 0 0 6px;
}

.total {
  background: #e8f4ec;
  color: #236745;
  font-size: 11px;
  border-radius: 20px;
  padding: 7px 10px;
  white-space: nowrap;
}

.filtros {
  display: grid;
  grid-template-columns: minmax(165px, 1fr) minmax(135px, 0.7fr) 95px;
  gap: 10px;
  align-items: end;
  margin-bottom: 19px;
}

.campo {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
}

.campo label {
  font-size: 11px;
  color: #486651;
  font-weight: 700;
}

.campo input,
.campo select {
  height: 39px;
  border: 1px solid #cedfd3;
  border-radius: 8px;
  padding: 8px 10px;
  font: inherit;
  font-size: 12px;
  min-width: 0;
  width: 100%;
  box-sizing: border-box;
  background: #fff;
  color: #254935;
  outline-color: #2a9963;
}

.acoes-filtro {
  display: flex;
  gap: 9px;
  grid-column: 1 / -1;
}

.botao-verde,
.botao-claro,
.botao-detalhe {
  font: inherit;
  font-size: 12px;
  border-radius: 8px;
  padding: 10px 13px;
  cursor: pointer;
  white-space: nowrap;
}

.botao-verde {
  color: #fff;
  background: #176d45;
  border: 1px solid #176d45;
}

.botao-verde:hover {
  background: #0d5632;
}

.botao-claro,
.botao-detalhe {
  color: #206841;
  background: #fff;
  border: 1px solid #d4e4d9;
}

.botao-claro:hover,
.botao-detalhe:hover {
  background: #eef8f1;
}

button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.tabela-rolagem {
  overflow-x: auto;
}

table {
  width: 100%;
  min-width: 720px;
  border-collapse: collapse;
}

th,
td {
  text-align: left;
  padding: 11px 10px;
  font-size: 12px;
  border-bottom: 1px solid #e8f0eb;
  vertical-align: middle;
}

th {
  color: #3e5d49;
  background: #eef7f0;
  font-weight: 700;
}

td {
  color: #3a5544;
}

td strong {
  display: block;
  color: #244c36;
  line-height: 1.4;
}

td small {
  display: block;
  font-size: 10px;
  color: #849889;
  margin-top: 4px;
}

.linha-selecionada {
  background: #f1f9f3;
}

.status {
  display: inline-block;
  font-weight: 700;
  font-size: 10px;
  border-radius: 20px;
  padding: 7px 9px;
  white-space: nowrap;
}

.status-pendente {
  background: #fff2d6;
  color: #94600d;
}

.status-aprovado {
  background: #e1f5e8;
  color: #1b7a46;
}

.status-rejeitado {
  background: #fde8e8;
  color: #a03333;
}

.conta-ativa {
  color: #1a7d48;
}

.conta-inativa {
  color: #9c3434;
}

.paginacao {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-top: 17px;
  color: #6a8071;
  font-size: 12px;
}

.paginacao > div {
  display: flex;
  gap: 7px;
}

.perfil-selecionado {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #eef8f1;
  border-radius: 10px;
  padding: 13px;
  margin-bottom: 12px;
}

.perfil-selecionado > div:last-child {
  display: flex;
  flex-direction: column;
  gap: 5px;
  min-width: 0;
}

.perfil-selecionado strong {
  font-size: 14px;
  overflow-wrap: anywhere;
}

.perfil-selecionado span {
  font-size: 11px;
  color: #65826e;
}

.avatar-vet {
  display: grid;
  place-items: center;
  flex-shrink: 0;
  background: #d5efdf;
  color: #206d45;
  border-radius: 50%;
  width: 39px;
  height: 39px;
  font-size: 21px;
}

.linha-detalhe {
  padding: 11px 0;
  border-bottom: 1px solid #e9f0ea;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
}

.linha-detalhe > span:first-child,
.detalhe-bloco > span {
  font-size: 12px;
  color: #698070;
}

.linha-detalhe strong {
  color: #234a34;
  font-size: 12px;
  text-align: right;
  overflow-wrap: anywhere;
}

.detalhe-bloco {
  margin-top: 14px;
}

.detalhe-bloco p {
  background: #f3f8f4;
  border: 1px solid #e4ede6;
  border-radius: 8px;
  padding: 12px;
  font-size: 12px;
  line-height: 1.5;
  color: #365442;
  overflow-wrap: anywhere;
}

.bloco-rejeicao p {
  background: #fff2f2;
  color: #8e3434;
  border-color: #fadada;
}

.aviso-pendente {
  margin-top: 20px;
  padding: 14px;
  background: #f2f8f3;
  border-radius: 9px;
}

.aviso-pendente strong {
  font-size: 13px;
  color: #17623d;
}

.aviso-pendente p {
  font-size: 12px;
  color: #67806c;
  line-height: 1.5;
}

.link-acao {
  display: inline-block;
  text-decoration: none;
  margin-top: 5px;
}

.alerta {
  border-radius: 9px;
  padding: 13px 15px;
  font-size: 13px;
  margin: 0 0 16px;
}

.alerta.erro {
  background: #fff1f1;
  color: #9e3030;
}

.mensagem {
  color: #75887b;
  font-size: 13px;
  text-align: center;
  padding: 35px 16px;
}

@media (max-width: 1280px) {
  .corpo-colunas {
    grid-template-columns: 1fr;
  }

  .detalhe-painel {
    order: 2;
  }
}

@media (max-width: 850px) {
  .estrutura {
    grid-template-columns: 1fr;
  }

  .menu-lateral {
    padding: 9px 14px;
    border-bottom: 1px solid #e5eee9;
    border-right: 0;
  }

  .navegacao {
    flex-direction: row;
    flex-wrap: wrap;
  }

  .menu-item.indisponivel,
  .rodape {
    display: none;
  }

  .conteudo {
    padding: 22px 15px;
  }

  .marca small {
    display: none;
  }
}

@media (max-width: 600px) {
  .cabecalho {
    padding: 12px;
  }

  .titulo-pagina {
    flex-direction: column;
  }

  .filtros {
    grid-template-columns: 1fr 1fr;
  }

  .campo.busca {
    grid-column: 1 / -1;
  }

  .campo-uf {
    grid-column: auto;
  }

  .paginacao {
    align-items: flex-start;
    flex-direction: column;
  }

  .marca strong {
    font-size: 15px;
  }

  .usuario {
    display: none;
  }
}
</style>

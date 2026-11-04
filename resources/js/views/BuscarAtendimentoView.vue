<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const cidadeBusca = computed(() => {
  return typeof route.query.cidade === 'string'
    ? route.query.cidade.trim()
    : ''
})

const ufBusca = computed(() => {
  return typeof route.query.uf === 'string'
    ? route.query.uf.trim()
    : ''
})

const cidade = computed(() => {
  return cidadeBusca.value || 'sua região'
})

const veterinarios = ref([])
const carregando = ref(false)
const mensagemErro = ref('')

const totalResultados = computed(() => veterinarios.value.length)

let pesquisaAtual = 0

function obterIniciais(nome) {
  if (!nome) return 'VT'

  return nome
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map(parte => parte[0]?.toUpperCase())
    .join('')
}

watch(
  [cidadeBusca, ufBusca],
  async () => {
    const identificador = ++pesquisaAtual

    veterinarios.value = []
    mensagemErro.value = ''

    if (!cidadeBusca.value) {
      carregando.value = false
      return
    }

    carregando.value = true

    try {
      const params = {
        cidade: cidadeBusca.value
      }

      if (ufBusca.value) {
        params.uf = ufBusca.value
      }

      const response = await axios.get('/api/veterinarios/buscar', { params })

      if (identificador !== pesquisaAtual) return

      const dados = response.data

      const lista = Array.isArray(dados)
        ? dados
        : Array.isArray(dados?.data)
          ? dados.data
          : null

      if (lista === null) {
        throw new Error('Formato inesperado da API')
      }

      veterinarios.value = lista
    } catch (error) {
      if (identificador !== pesquisaAtual) return

      console.error('Erro ao buscar veterinários:', error)

      mensagemErro.value =
        'Não foi possível carregar os profissionais no momento. Tente novamente em instantes.'
    } finally {
      if (identificador === pesquisaAtual) {
        carregando.value = false
      }
    }
  },
  { immediate: true }
)
</script>

<template>
  <main class="search-page">
    <section class="search-hero">
      <div class="hero-overlay"></div>

      <div class="search-container">
        <div class="hero-content">
          <span class="hero-badge">Busca pública de atendimento</span>

          <h1>Atendimento veterinário em {{ cidade }}</h1>

          <p class="hero-description">
            Conheça profissionais veterinários da sua região e encontre
            opções  de atendimento para cuidar do seu pet.
          </p>

          <div class="hero-meta" v-if="cidadeBusca && !carregando && !mensagemErro">
            <div class="meta-card">
              <strong>{{ totalResultados }}</strong>
              <span>resultado<span v-if="totalResultados !== 1">s</span> encontrado<span v-if="totalResultados !== 1">s</span></span>
            </div>

            <div class="meta-card">
              <strong>{{ cidade }}</strong>
              <span>cidade pesquisada</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="results-section">
      <div class="search-container">
        <!-- cidade não informada -->
        <div v-if="!cidadeBusca" class="feedback-card neutral">
          <h2>Informe uma cidade para começar</h2>
          <p>
            Digite uma cidade para encontrar profissionais disponíveis na busca pública.
          </p>
        </div>

        <!-- carregando -->
        <div v-else-if="carregando" class="feedback-card neutral">
          <h2>Buscando veterinários...</h2>
          <p>
            Estamos carregando os profissionais disponíveis para sua pesquisa.
          </p>
        </div>

        <!-- erro -->
        <div v-else-if="mensagemErro" class="feedback-card error">
          <h2>Ops! Algo deu errado</h2>
          <p>{{ mensagemErro }}</p>
        </div>

        <!-- vazio -->
        <div v-else-if="veterinarios.length === 0" class="feedback-card neutral">
          <h2>Nenhum veterinário encontrado</h2>
          <p>
            Não encontramos profissionais para essa localização no momento.
            Tente outra cidade ou revise a busca.
          </p>
        </div>

        <!-- lista -->
        <div v-else class="results-wrapper">
          <div class="results-header">
            <div>
              <span class="section-label">Profissionais encontrados</span>
              <h2>Resultados da sua busca</h2>
            </div>

            <div class="results-pill">
              {{ totalResultados }} profissional<span v-if="totalResultados !== 1">is</span>
            </div>
          </div>

          <div class="veterinarios-grid">
            <article
              v-for="veterinario in veterinarios"
              :key="veterinario.id"
              class="veterinario-card"
            >
              <div class="card-top">
                <div class="avatar-badge">
                  {{ obterIniciais(veterinario.nome) }}
                </div>

                <div class="card-title">
                  <h3>{{ veterinario.nome }}</h3>
                  <p>{{ veterinario.especialidade || 'Especialidade não informada' }}</p>
                </div>
              </div>

              <div class="card-divider"></div>

              <div class="card-info-list">
                <div class="info-item">
                  <span class="info-label">CRMV</span>
                  <strong>{{ veterinario.crmv }}</strong>
                </div>

                <div class="info-item">
                  <span class="info-label">Localização</span>
                  <strong>
                    {{ veterinario.cidade }}<span v-if="veterinario.uf">/{{ veterinario.uf }}</span>
                  </strong>
                </div>
              </div>

              <div class="card-footer">
                <span class="status-chip">Profissional encontrado</span>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<style scoped>
.search-page {
  min-height: 100vh;
  background:
    radial-gradient(circle at top left, rgba(19, 170, 130, 0.10), transparent 32%),
    radial-gradient(circle at top right, rgba(11, 41, 66, 0.08), transparent 28%),
    linear-gradient(180deg, #f6fbf9 0%, #f8fcfa 42%, #ffffff 100%);
}

.search-container {
  width: 100%;
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 24px;
}

.search-hero {
  position: relative;
  overflow: hidden;
  padding: 72px 0 36px;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    90deg,
    rgba(19, 170, 130, 0.03) 0%,
    rgba(11, 41, 66, 0.02) 100%
  );
  pointer-events: none;
}

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 760px;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  padding: 10px 16px;
  border-radius: 999px;
  background: rgba(19, 170, 130, 0.12);
  color: #149d78;
  font-size: 14px;
  font-weight: 600;
}

.search-hero h1 {
  margin: 18px 0 14px;
  color: #0b2942;
  font-size: 52px;
  line-height: 1.08;
  letter-spacing: -0.02em;
}

.hero-description {
  max-width: 640px;
  color: #58718b;
  font-size: 18px;
  line-height: 1.7;
}

.hero-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-top: 28px;
}

.meta-card {
  min-width: 190px;
  padding: 18px 20px;
  border: 1px solid rgba(19, 170, 130, 0.10);
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.82);
  box-shadow: 0 14px 40px rgba(11, 41, 66, 0.05);
  backdrop-filter: blur(10px);
}

.meta-card strong {
  display: block;
  margin-bottom: 4px;
  color: #0b2942;
  font-size: 24px;
  font-weight: 700;
}

.meta-card span {
  color: #58718b;
  font-size: 14px;
}

.results-section {
  padding: 18px 0 72px;
}

.feedback-card {
  padding: 32px;
  border-radius: 24px;
  border: 1px solid #e5efea;
  background: #ffffff;
  box-shadow: 0 18px 50px rgba(11, 41, 66, 0.06);
}

.feedback-card h2 {
  margin: 0 0 10px;
  color: #0b2942;
  font-size: 24px;
}

.feedback-card p {
  margin: 0;
  color: #58718b;
  font-size: 16px;
  line-height: 1.7;
}

.feedback-card.error {
  border-color: #f6d3d1;
  background: #fff6f5;
}

.results-wrapper {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.results-header {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.section-label {
  display: inline-block;
  margin-bottom: 8px;
  color: #149d78;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.results-header h2 {
  margin: 0;
  color: #0b2942;
  font-size: 30px;
}

.results-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 12px 18px;
  border-radius: 999px;
  background: #0b2942;
  color: #ffffff;
  font-size: 14px;
  font-weight: 600;
}

.veterinarios-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
  gap: 22px;
}

.veterinario-card {
  position: relative;
  padding: 24px;
  border-radius: 24px;
  background: linear-gradient(180deg, #ffffff 0%, #fbfefd 100%);
  border: 1px solid #e3eee9;
  box-shadow: 0 16px 40px rgba(11, 41, 66, 0.07);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.veterinario-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 22px 50px rgba(11, 41, 66, 0.10);
}

.card-top {
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.avatar-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 56px;
  height: 56px;
  border-radius: 18px;
  background: linear-gradient(135deg, #13aa82 0%, #0b2942 100%);
  color: #ffffff;
  font-size: 18px;
  font-weight: 700;
  box-shadow: 0 10px 22px rgba(19, 170, 130, 0.24);
}

.card-title h3 {
  margin: 0 0 6px;
  color: #0b2942;
  font-size: 24px;
  line-height: 1.25;
}

.card-title p {
  margin: 0;
  color: #58718b;
  font-size: 15px;
}

.card-divider {
  height: 1px;
  margin: 22px 0 18px;
  background: linear-gradient(
    90deg,
    rgba(19, 170, 130, 0.25) 0%,
    rgba(11, 41, 66, 0.05) 100%
  );
}

.card-info-list {
  display: grid;
  gap: 14px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-label {
  color: #7a90a5;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.info-item strong {
  color: #0b2942;
  font-size: 16px;
  font-weight: 600;
}

.card-footer {
  margin-top: 22px;
}

.status-chip {
  display: inline-flex;
  align-items: center;
  padding: 10px 14px;
  border-radius: 999px;
  background: rgba(19, 170, 130, 0.10);
  color: #149d78;
  font-size: 13px;
  font-weight: 700;
}

@media (max-width: 900px) {
  .search-hero h1 {
    font-size: 40px;
  }

  .hero-description {
    font-size: 16px;
  }

  .results-header h2 {
    font-size: 26px;
  }
}

@media (max-width: 640px) {
  .search-container {
    padding: 0 16px;
  }

  .search-hero {
    padding: 52px 0 24px;
  }

  .search-hero h1 {
    font-size: 32px;
  }

  .hero-meta {
    flex-direction: column;
  }

  .meta-card {
    width: 100%;
  }

  .feedback-card,
  .veterinario-card {
    padding: 22px;
  }

  .card-title h3 {
    font-size: 21px;
  }

  .results-header h2 {
    font-size: 24px;
  }
}
</style>
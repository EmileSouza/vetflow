<script setup>
import { computed, onMounted, ref } from 'vue'
import adminApi from '../../services/adminApi'

// Dados recebidos do Laravel
const semanas = ref([])
const totalPeriodo = ref(null)
const periodo = ref('Últimos 28 dias')

// Estados da interface
const carregando = ref(true)
const erro = ref('')

// Maior quantidade encontrada.
// Serve como referência para a altura das barras.
const maiorQuantidade = computed(() => {
  const valores = semanas.value.map(
    (semana) => semana.quantidade
  )

  return Math.max(1, ...valores)
})

// Verifica se o período tem agendamentos.
const existemAgendamentos = computed(() => {
  return semanas.value.some(
    (semana) => semana.quantidade > 0
  )
})

// Formata os números no padrão brasileiro.
function formatarNumero(valor) {
  return new Intl.NumberFormat('pt-BR').format(
    Number(valor) || 0
  )
}

// Calcula a altura proporcional de cada barra.
function alturaBarra(quantidade) {
  const porcentagem =
    (quantidade / maiorQuantidade.value) * 100

  return `${porcentagem}%`
}

// Formata YYYY-MM-DD como DD/MM.
function formatarDataCurta(valor) {
  if (!valor) return '—'

  const partes = valor.split('-')

  if (partes.length !== 3) return '—'

  return `${partes[2]}/${partes[1]}`
}

// Consulta os dados reais no backend.
async function recarregar() {
  carregando.value = true
  erro.value = ''

  try {
    const resposta = await adminApi.get(
      '/admin/dashboard/agendamentos-por-periodo'
    )

    const dados = resposta.data.data

    if (!Array.isArray(dados?.semanas)) {
      throw new Error('Resposta inválida da API.')
    }

    semanas.value = dados.semanas.map(
      (semana, indice) => ({
        rotulo: semana.rotulo || `Sem ${indice + 1}`,
        inicio: semana.inicio,
        fim: semana.fim,
        quantidade: Math.max(
          0,
          Number(semana.quantidade) || 0
        )
      })
    )

    totalPeriodo.value =
      Number(dados.total_periodo) || 0

    periodo.value =
      dados.periodo || 'Últimos 28 dias'

  } catch (error) {
    semanas.value = []
    totalPeriodo.value = null

    if ([401, 403, 419].includes(error.response?.status)) {
      erro.value =
        'Sua sessão não permite consultar estes indicadores.'
    } else {
      erro.value =
        'Não foi possível carregar os agendamentos.'
    }
  } finally {
    carregando.value = false
  }
}

// Permite que o Dashboard atualize o gráfico
// quando a pessoa clicar em "Atualizar dados".
defineExpose({
  recarregar
})

onMounted(recarregar)
</script>

<template>
  <section
    class="grafico-panel"
    aria-labelledby="titulo-grafico"
  >
    <!-- CABEÇALHO -->
    <div class="grafico-header">
      <div>
        <h3 id="titulo-grafico">
          Agendamentos por período
        </h3>

        <p>
          Quantidade de consultas agendadas
          nas últimas quatro semanas.
        </p>
      </div>

      <span class="periodo-label">
        {{ periodo }}
      </span>
    </div>

    <!-- CARREGAMENTO -->
    <div
      v-if="carregando"
      class="grafico-estado"
      role="status"
    >
      Carregando dados dos agendamentos...
    </div>

    <!-- ERRO -->
    <div
      v-else-if="erro"
      class="grafico-estado"
      role="alert"
    >
      <p>{{ erro }}</p>

      <button
        type="button"
        class="tentar-novamente"
        @click="recarregar"
      >
        Tentar novamente
      </button>
    </div>

    <!-- GRÁFICO -->
    <template v-else>
      <div class="resumo-periodo">
        <div>
          <span>Total no período</span>

          <strong>
            {{ formatarNumero(totalPeriodo) }}
          </strong>
        </div>

        <span class="resumo-legenda">
          Consultas agendadas
        </span>
      </div>

      <div
        class="grafico-barras"
        aria-label="Distribuição semanal dos agendamentos"
      >
        <div
          v-for="(semana, indice) in semanas"
          :key="semana.rotulo"
          class="coluna"
          role="img"
          :aria-label="
            `${semana.rotulo}: ${semana.quantidade} agendamentos`
          "
        >
          <!-- QUANTIDADE -->
          <span class="quantidade">
            {{ formatarNumero(semana.quantidade) }}
          </span>

          <!-- ÁREA VERTICAL DA BARRA -->
          <div class="barra-espaco">
            <div
              class="barra"
              :class="{
                'barra-destaque': indice === 3
              }"
              :style="{
                height: alturaBarra(semana.quantidade)
              }"
            ></div>
          </div>

          <!-- NOME DA SEMANA -->
          <strong class="semana-label">
            {{ semana.rotulo }}
          </strong>

          <!-- DATAS -->
          <small class="datas">
            {{ formatarDataCurta(semana.inicio) }}
            –
            {{ formatarDataCurta(semana.fim) }}
          </small>
        </div>
      </div>

      <p
        v-if="!existemAgendamentos"
        class="sem-agendamentos"
      >
        Nenhuma consulta agendada neste período.
      </p>

      <div class="grafico-rodape">
        <span class="legenda-cor"></span>

        <span>
          Quantidade de agendamentos por semana
        </span>
      </div>
    </template>
  </section>
</template>

<style scoped>
.grafico-panel {
  background: #ffffff;
  border: 1px solid #e1e8e2;
  border-radius: 14px;
  padding: 23px;
  box-shadow: 0 4px 14px #124d3810;
  min-width: 0;
}

.grafico-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
}

.grafico-header h3 {
  font-size: 18px;
  color: #193e2d;
  margin: 0 0 6px;
}

.grafico-header p {
  margin: 0;
  font-size: 12px;
  color: #73887b;
  line-height: 1.5;
}

.periodo-label {
  background: #e9f5ed;
  color: #236b45;
  border-radius: 20px;
  padding: 7px 10px;
  font-size: 11px;
  white-space: nowrap;
}

.resumo-periodo {
  margin-top: 20px;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 10px;
}

.resumo-periodo > div {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.resumo-periodo span {
  font-size: 12px;
  color: #678271;
}

.resumo-periodo strong {
  color: #155b3d;
  font-size: 29px;
}

.resumo-legenda {
  text-align: right;
}

/* ÁREA DAS QUATRO COLUNAS */
.grafico-barras {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin-top: 22px;
}

.coluna {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 0;
}

.quantidade {
  color: #35664b;
  font-weight: 700;
  font-size: 13px;
  margin-bottom: 8px;
}

/* A altura é igual para as quatro semanas. */
.barra-espaco {
  height: 155px;
  width: 100%;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  border-bottom: 1px solid #d9e7dc;
  background-image: linear-gradient(
    to top,
    transparent 24%,
    #edf2ee 25%,
    transparent 26%,
    transparent 49%,
    #edf2ee 50%,
    transparent 51%,
    transparent 74%,
    #edf2ee 75%,
    transparent 76%
  );
}

.barra {
  width: min(100%, 70px);
  background: linear-gradient(
    180deg,
    #91cfaa,
    #55aa78
  );
  border-radius: 8px 8px 0 0;
  transition: height 0.35s ease;
}

.barra-destaque {
  background: linear-gradient(
    180deg,
    #268755,
    #155d3b
  );
}

.semana-label {
  margin-top: 12px;
  color: #345b42;
  font-size: 12px;
}

.datas {
  margin-top: 5px;
  color: #8b9b8e;
  font-size: 10px;
  text-align: center;
}

.sem-agendamentos {
  text-align: center;
  color: #879a8c;
  font-size: 12px;
  margin: 15px 0 0;
}

.grafico-rodape {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 19px;
  padding-top: 12px;
  border-top: 1px solid #e9efe9;
  color: #74887b;
  font-size: 11px;
}

.legenda-cor {
  width: 11px;
  height: 11px;
  border-radius: 3px;
  background: #55aa78;
}

.grafico-estado {
  min-height: 250px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  gap: 12px;
  color: #6a8271;
  font-size: 13px;
}

.tentar-novamente {
  border: 1px solid #c9e2d1;
  background: #e9f5ed;
  color: #17623e;
  padding: 10px 15px;
  border-radius: 8px;
  cursor: pointer;
}

@media (max-width: 560px) {
  .grafico-panel {
    padding: 17px;
  }

  .grafico-header {
    flex-direction: column;
  }

  .resumo-periodo {
    align-items: flex-start;
  }

  .grafico-barras {
    gap: 7px;
  }

  .barra-espaco {
    height: 125px;
  }

  .datas {
    font-size: 9px;
  }
}
</style>

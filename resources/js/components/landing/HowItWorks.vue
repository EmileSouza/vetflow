<template>
<!-- COMO FUNCIONA -->
<section id="como-funciona" class="how-it-works">

  <div class="section-title">
    <h2>Como funciona?</h2>
    <p>Veja como é simples encontrar um atendimento no VetFlow.</p>
  </div>

  <div class="steps-container">

    <div class="step-card">
      <span class="step-number">1</span>

      <h3>Busque</h3>

      <p>
        Informe sua cidade e encontre clínicas e veterinários disponíveis.
      </p>
    </div>

    <span class="step-arrow">›</span>

    <div class="step-card">
      <span class="step-number">2</span>

      <h3>Escolha</h3>

      <p>
        Veja os horários disponíveis e selecione o que melhor funciona para você.
      </p>
    </div>

    <span class="step-arrow">›</span>

    <div class="step-card">
      <span class="step-number">3</span>

      <h3>Solicite</h3>

      <p>
        Preencha os dados do seu pet e envie a solicitação de consulta.
      </p>
    </div>

    <span class="step-arrow">›</span>

    <div class="step-card">
      <span class="step-number">4</span>

      <h3>Aguarde a confirmação</h3>

      <p>
        O veterinário ou clínica confirma o atendimento e você recebe todas as informações.
      </p>
    </div>

  </div>

</section>
</template>
<style scoped>
/* COMO FUNCIONA */

.how-it-works {
  padding: 45px 24px 55px;
  background: #ffffff;
}

.section-title {
  margin-bottom: 30px;
  text-align: center;
}

.section-title h2 {
  margin: 0 0 8px;

  color: #0b2942;
  font-size: 30px;
  font-weight: 700;
}

.section-title p {
  margin: 0;

  color: #58718b;
  font-size: 14px;
}

.steps-container {
  max-width: 1200px;
  margin: 0 auto;

  display: flex;
  align-items: center;
  justify-content: space-between;
}

.step-card {
  width: 235px;
  min-height: 175px;
  padding: 22px;

  background: #ffffff;
  border: 1px solid #e3e9e7;
  border-radius: 12px;

  transition: 0.2s;
}

.step-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(11, 41, 66, 0.08);
}

.step-number {
  width: 46px;
  height: 46px;

  display: flex;
  align-items: center;
  justify-content: center;

  margin-bottom: 13px;

  background: #d9f3eb;
  border-radius: 50%;

  color: #009b72;
  font-size: 24px;
  font-weight: 700;
}

.step-card h3 {
  margin: 0 0 7px;

  color: #0b2942;
  font-size: 16px;
  font-weight: 700;
}

.step-card p {
  margin: 0;

  color: #58718b;
  font-size: 13px;
  line-height: 1.5;
}

.step-arrow {
  color: #009b72;
  font-size: 38px;
  font-weight: 300;
}
</style>
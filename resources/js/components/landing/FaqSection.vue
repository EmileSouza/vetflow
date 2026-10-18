<template>
<!-- PERGUNTAS FREQUENTES -->
<section id="duvidas" class="faq-section">

  <div class="section-title">
    <h2>Perguntas frequentes</h2>

    <p>
      Tire suas dúvidas sobre o VetFlow. Se não encontrar o que procura,
      entre em contato conosco.
    </p>
  </div>

  <div class="faq-container">

    <details class="faq-item">
      <summary>
        Preciso criar uma conta para solicitar uma consulta?
        <span>⌄</span>
      </summary>

      <p>
        Não. Você poderá solicitar um atendimento informando seus dados
        de contato e os dados do seu pet.
      </p>
    </details>

    <details class="faq-item">
      <summary>
        Como sei se minha consulta foi confirmada?
        <span>⌄</span>
      </summary>

      <p>
        Após enviar a solicitação, ela ficará aguardando a confirmação
        do veterinário. Quando o atendimento for confirmado, você receberá
        as informações da consulta.
      </p>
    </details>

    <details class="faq-item">
      <summary>
        Posso cancelar uma solicitação?
        <span>⌄</span>
      </summary>

      <p>
        Sim. O cancelamento poderá ser realizado de acordo com o status
        da solicitação e as regras do atendimento.
      </p>
    </details>

    <details class="faq-item">
      <summary>
        O VetFlow realiza atendimento veterinário?
        <span>⌄</span>
      </summary>

      <p>
        Não. O VetFlow é uma plataforma que conecta tutores a profissionais
        e facilita a organização e solicitação de atendimentos veterinários.
      </p>
    </details>

    <details class="faq-item">
      <summary>
        Como faço para me cadastrar como veterinário?
        <span>⌄</span>
      </summary>

      <p>
        O profissional poderá realizar seu cadastro no VetFlow e informar
        seus dados profissionais. Após a verificação, seu perfil poderá
        ser disponibilizado na plataforma.
      </p>
    </details>

  </div>

</section>
</template>
<style scoped>
/* PERGUNTAS FREQUENTES */
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

.faq-section {
  padding: 0 24px 50px;
  background: #ffffff;
}

.faq-container {
  max-width: 1200px;
  margin: 0 auto;
}

.faq-item {
  margin-bottom: 9px;

  background: #ffffff;
  border: 1px solid #e3e9e7;
  border-radius: 9px;

  overflow: hidden;
}

.faq-item summary {
  min-height: 50px;
  padding: 0 20px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  color: #0b2942;
  font-size: 14px;
  font-weight: 500;

  cursor: pointer;
  list-style: none;
}

.faq-item summary::-webkit-details-marker {
  display: none;
}

.faq-item summary span {
  color: #0b2942;
  font-size: 22px;

  transition: transform 0.2s;
}

.faq-item[open] summary span {
  transform: rotate(180deg);
}

.faq-item > p {
  margin: 0;
  padding: 0 20px 18px;

  color: #58718b;
  font-size: 13px;
  line-height: 1.6;
}
</style>
describe('Login do VetFlow', () => {
  it('deve exibir a tela de login corretamente', () => {
    cy.visit('http://vetflow.test/login')

    cy.contains('Bem-vindo de volta!').should('be.visible')
    cy.contains('E-mail').should('be.visible')
    cy.contains('Senha').should('be.visible')
    cy.contains('Entrar').should('be.visible')
    cy.contains('Esqueceu a senha?').should('be.visible')
    cy.contains('Cadastre-se').should('be.visible')
  })
})
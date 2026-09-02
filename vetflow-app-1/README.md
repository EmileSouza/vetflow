# VetFlow

VetFlow é um sistema de gestão para clínicas veterinárias, projetado para conectar tecnologia e cuidados com os animais. Este projeto é construído com Vue 3 e utiliza Vite como bundler.

## Estrutura do Projeto

A estrutura do projeto é organizada da seguinte forma:

```
vetflow-app
├── public
│   └── favicon.svg
├── src
│   ├── App.vue
│   ├── main.js
│   ├── assets
│   │   └── styles
│   │       └── index.css
│   ├── components
│   │   ├── layout
│   │   │   ├── AppSidebar.vue
│   │   │   ├── AppHeader.vue
│   │   │   └── AppShell.vue
│   │   └── ui
│   │       ├── Card.vue
│   │       └── StatCard.vue
│   ├── composables
│   │   └── useAuth.js
│   ├── layouts
│   │   └── DefaultLayout.vue
│   ├── pages
│   │   ├── Dashboard.vue
│   │   ├── Patients.vue
│   │   ├── Services.vue
│   │   ├── Settings.vue
│   │   └── Login.vue
│   ├── router
│   │   └── index.js
│   ├── stores
│   │   └── auth.js
│   └── utils
│       └── formatters.js
├── .gitignore
├── .env.example
├── index.html
├── jsconfig.json
├── package.json
├── vite.config.js
├── README.md
└── eslint.config.js
```

## Instalação

Para instalar e executar o projeto, siga os passos abaixo:

1. Clone o repositório:
   ```
   git clone <URL_DO_REPOSITORIO>
   cd vetflow-app
   ```

2. Instale as dependências:
   ```
   npm install
   ```

3. Inicie o servidor de desenvolvimento:
   ```
   npm run dev
   ```

4. Acesse a aplicação em seu navegador em `http://localhost:3000`.

## Funcionalidades

- **Autenticação**: Sistema de login para usuários.
- **Dashboard**: Visualização de dados e estatísticas principais.
- **Gerenciamento de Pacientes**: Adicione, edite e visualize informações sobre pacientes.
- **Serviços**: Liste e gerencie os serviços oferecidos pela clínica.
- **Configurações**: Ajuste preferências e configurações do sistema.

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir um issue ou enviar um pull request.

## Licença

Este projeto está licenciado sob a MIT License. Veja o arquivo LICENSE para mais detalhes.
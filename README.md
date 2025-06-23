  # Teste Técnico – Desenvolvedor PHP Pleno

  ## Objetivo

  Desenvolver uma aplicação web com Laravel para controle de **estacionamento rotativo urbano**. A aplicação deve permitir registrar veículos, vagas e operações de entrada/saída. Toda a aplicação deve ser exposta via **API REST JSON**.

  ---

  ## Funcionalidades obrigatórias

  ### CRUD de Vagas

  - Campos: código da vaga, localização (rua, número, bairro), status (livre, ocupada, interditada).
  - Paginação, ordenação e filtros por status e localização.

  ### CRUD de Veículos

  - Campos: placa, modelo, cor, tipo (carro, moto).
  - Validação da placa (Mercosul).
  - Um veículo pode ter múltiplas entradas no histórico.

  ### Operações de Estacionamento

  - Registrar entrada de um veículo em uma vaga.
  - Registrar saída, calculando tempo total e valor (ex: R$ 2,00/hora, fracionado).
  - Regras:
    - Não permitir entrada se a vaga estiver ocupada ou interditada.
    - Não permitir saída se o veículo não estiver estacionado.

  ---

  ## API REST

  - Todos os módulos devem expor endpoints JSON completos (CRUD e operações).
  - Usar `Resource` e `FormRequest` do Laravel.

  ---

  ## Autenticação

  - A API deve requerer autenticação via **token** (Laravel Sanctum ou Passport).
  - Criar usuário via seed ou endpoint `/register`.

  ---

  ## Requisitos Técnicos

  - Laravel (última versão).
  - Docker com `docker-compose` (banco e app).
  - Banco: MySQL ou PostgreSQL.
  - Migrations, Seeders e Factories obrigatórios.

  ---

  ## Bônus

  - Dashboard HTML com:
    - Vagas ocupadas em tempo real.
    - Total de veículos por dia.
    - Receita gerada por período.
  - PDF do histórico de estacionamento.
  - Testes de API (`Feature Tests`) com cobertura mínima para entrada/saída e regras de negócio.

  ---

  ## Entrega

  - Para iniciar o teste, faça um fork deste repositório; Se você apenas clonar o repositório não vai conseguir fazer push.
  - Crie uma branch com o seu nome completo;
  - Altere o arquivo README.md com as informações necessárias para executar o seu teste (comandos, migrations, seeds, etc);
  - Depois de finalizado, envie-nos o pull request;

  ---

  ## Critérios de Avaliação

  - Clareza e legibilidade do código.
  - Separação de responsabilidades.
  - Uso correto de recursos do Laravel.
  - API bem estruturada (RESTful, validações, status codes).
  - Clean Code, SOLID, testes.
  - Setup funcional com Docker.

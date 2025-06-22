# Teste Técnico – Estacionamento Rotativo Urbano

Este projeto é a solução para o teste técnico de Desenvolvedor PHP Pleno, focado na construção de uma API RESTful completa para um sistema de estacionamento rotativo urbano. Adicionalmente, inclui um dashboard administrativo como funcionalidade bônus.

## Sumário das Funcionalidades

* **Autenticação de Usuários:**
    * Endpoints para registro (`/register`) e login (`/login`) de usuários, utilizando Laravel Sanctum para autenticação via token.
* **CRUD de Vagas:**
    * Gerenciamento completo (Criar, Ler, Atualizar, Deletar) de vagas de estacionamento.
    * Campos como `código`, `rua`, `numero`, `bairro` e `status` (livre, ocupada, interditada).
    * Suporte a paginação, ordenação e filtros por status e localização.
* **CRUD de Veículos:**
    * Gerenciamento completo (Criar, Ler, Atualizar, Deletar) de veículos.
    * Campos incluem `placa`, `modelo`, `cor` e `tipo` (carro, moto).
    * Validação de placa no padrão Mercosul.
    * Um veículo pode ter múltiplas entradas no histórico de estacionamento.
* **Operações de Estacionamento:**
    * **Registrar Entrada:** Permite registrar a entrada de um veículo em uma vaga específica.
        * **Regra:** Não permite entrada se a vaga estiver `ocupada` ou `interditada`.
    * **Registrar Saída:** Registra a saída de um veículo, calculando automaticamente o tempo total de permanência e o valor a ser pago (com base em R$ 2,00/hora, fracionado).
        * **Regra:** Não permite saída se o veículo não estiver atualmente estacionado.
* **API RESTful:**
    * Todos os módulos e operações são expostos via endpoints JSON, seguindo os princípios RESTful.
    * Utiliza recursos do Laravel como `Resource` para formatação das respostas e `FormRequest` para validação de requisições.
* **Dashboard HTML (Bônus):**
    * Um painel administrativo para visualização rápida de métricas importantes:
        * Número de vagas ocupadas em tempo real e percentual de ocupação.
        * Total de veículos que entraram/saíram no dia atual.
        * Receita total gerada por diferentes períodos (hoje, esta semana, este mês).
    * **Geração de PDF:** Permite gerar um relatório em PDF com o histórico detalhado de estacionamentos.

## Requisitos do Sistema

Para rodar este projeto, você precisará ter instalado em sua máquina:

* **Docker:** Para gerenciar o ambiente de desenvolvimento (PHP, Nginx, MySQL).
* **Docker Compose:** Ferramenta para definir e rodar aplicativos Docker multi-contêiner.

## Como Configurar e Rodar o Projeto

Siga os passos abaixo para colocar a aplicação em funcionamento:

1.  **Clone o Repositório:**
    Abra seu terminal e clone o projeto para o seu ambiente local.
    ```bash
    git clone https://github.com/HEITORNERY/teste-desenvolvedor-backend.git
    ```

2.  **Configurar o Ambiente Docker com Sail:**
    O projeto utiliza [Laravel Sail](https://laravel.com/docs/sail) para um ambiente de desenvolvimento Docker leve e pré-configurado.

    * **Inicie os Contêineres:**
        Este comando construirá as imagens Docker (se for a primeira vez) e iniciará os serviços (PHP, Nginx, MySQL) em segundo plano.
        ```bash
        cd estacionamento-api
        ./vendor/bin/sail up -d
        ```
        Aguarde alguns instantes para que todos os serviços estejam completamente online.

    * **Instalar Dependências do Composer:**
        Entre no contêiner `laravel.test` (o serviço PHP) e instale todas as dependências do projeto via Composer.
        ```bash
        cd estacionamento-api
        ./vendor/bin/sail composer install
        ```

3.  **Executar Migrações e Seeds do Banco de Dados:**
    As migrações criarão as tabelas necessárias no seu banco de dados, e os seeders podem ser usados para popular o banco com dados de teste, como um usuário inicial e algumas vagas/veículos.

    * **Execute as Migrações e Seeds:**
        ```bash
        cd estacionamento-api
        ./vendor/bin/sail artisan migrate --seed
        ```
    * **Dados de Teste (Exemplo de Seeder):**
        O seeder padrão (`database/seeders/DatabaseSeeder.php`) pode ser configurado para criar um usuário administrativo padrão para que você possa testar a autenticação.
        Se você alterou o seeder, execute o comando `migrate:fresh --seed` (ATENÇÃO: apaga e recria o banco) ou `db:seed` (apenas roda os seeds):
        ```bash
        cd estacionamento-api
        ./vendor/bin/sail artisan migrate:fresh --seed
        # OU
        cd estacionamento-api
        ./vendor/bin/sail artisan db:seed
        ```

5.  **Acessar a Aplicação:**
    Após todos os passos anteriores, sua aplicação estará rodando.

    * **API RESTful:** A base da API estará disponível em `http://localhost/api/`.
    * **Dashboard HTML (Bônus):** O painel administrativo estará acessível em `http://localhost/dashboard`.

## Endpoints da API

Para interagir com a API, você precisará de um cliente HTTP (como Postman, Insomnia ou Thunder Client para VS Code).

### **Autenticação**

| Método | Endpoint                    | Descrição                                                              | Corpo da Requisição (JSON)                      | Resposta de Sucesso (Exemplo)                           | Status |
| :----- | :-------------------------- | :--------------------------------------------------------------------- | :---------------------------------------------- | :------------------------------------------------------ | :----- |
| `POST` | `/api/register`             | Registra um novo usuário e retorna um token de acesso.                 | `{ "name": "...", "email": "...", "password": "...", "password_confirmation": "..." }` | `{ "message": "...", "token": "...", "user": {} }`      | `201`  |
| `POST` | `/api/login`                | Realiza login com credenciais e retorna um novo token de acesso.      | `{ "email": "...", "password": "..." }`       | `{ "message": "...", "token": "...", "user": {} }`      | `200`  |
| `POST` | `/api/logout`               | Invalida o token de acesso do usuário autenticado.                      | (Nenhum)                                        | `{ "message": "Logout bem-sucedido!" }`                 | `200`  |
| `GET`  | `/api/user`                 | Retorna os dados do usuário atualmente autenticado.                    | (Nenhum)                                        | `{ "id": ..., "name": "...", "email": "..." }`          | `200`  |

* **Header de Autenticação:** Para todas as rotas protegidas (marcadas com `Sim` na tabela abaixo), você deve incluir o seguinte header na requisição:
    `Authorization: Bearer <seu_token_aqui>`

### **Veículos**

| Método | Endpoint                    | Descrição                          | Corpo da Requisição (JSON)                                | Requer Auth |
| :----- | :-------------------------- | :--------------------------------- | :-------------------------------------------------------- | :---------- |
| `GET`  | `/api/veiculos`             | Lista todos os veículos.            | (Nenhum)                                                  | Sim         |
| `POST` | `/api/veiculos`             | Cria um novo veículo.              | `{ "placa": "ABC1D23", "modelo": "Gol", "cor": "Preto", "tipo": "carro" }` | Sim         |
| `GET`  | `/api/veiculos/{id}`        | Exibe detalhes de um veículo.      | (Nenhum)                                                  | Sim         |
| `PUT`  | `/api/veiculos/{id}`        | Atualiza um veículo existente.     | `{ "placa": "XYZ9L87", "cor": "Azul" }` (apenas campos a atualizar) | Sim         |
| `DELETE`| `/api/veiculos/{id}`        | Exclui um veículo.                 | (Nenhum)                                                  | Sim         |

### **Vagas**

| Método | Endpoint                    | Descrição                                  | Corpo da Requisição (JSON)                                              | Requer Auth |
| :----- | :-------------------------- | :----------------------------------------- | :---------------------------------------------------------------------- | :---------- |
| `GET`  | `/api/vagas`                | Lista todas as vagas (suporta filtros e paginação: `?status=livre&page=1`). | (Nenhum)                                                                | Sim         |
| `POST` | `/api/vagas`                | Cria uma nova vaga.                        | `{ "codigo": "VAGA-A01", "localizacao_rua": "Rua Principal", "localizacao_numero": "123", "localizacao_bairro": "Centro", "status": "livre" }` | Sim         |
| `GET`  | `/api/vagas/{id}`           | Exibe detalhes de uma vaga.                | (Nenhum)                                                                | Sim         |
| `PUT`  | `/api/vagas/{id}`           | Atualiza uma vaga existente.               | `{ "status": "interditada" }` (apenas campos a atualizar)               | Sim         |
| `DELETE`| `/api/vagas/{id}`           | Exclui uma vaga.                           | (Nenhum)                                                                | Sim         |

### **Estacionamento (Operações de Entrada e Saída)**

| Método | Endpoint                    | Descrição                                         | Corpo da Requisição (JSON)                                      | Requer Auth |
| :----- | :-------------------------- | :------------------------------------------------ | :-------------------------------------------------------------- | :---------- |
| `POST` | `/api/estacionamento/entrada` | Registra a entrada de um veículo em uma vaga.     | `{ "veiculo_id": 1, "vaga_id": 1 }`                             | Sim         |
| `PUT`  | `/api/estacionamento/saida/{id}` | Registra a saída e calcula o valor total. `id` é o ID do registro de estacionamento (não do veículo ou vaga). | (Nenhum)                                                        | Sim         |
| `GET`  | `/api/estacionamento`       | Lista todos os registros de estacionamento.      | (Nenhum)                                                        | Sim         |
| `GET`  | `/api/estacionamento/{id}`  | Exibe detalhes de um registro de estacionamento. | (Nenhum)                                                        | Sim         |

## Dashboard e Relatório PDF

* **Dashboard HTML:**
    * Acesse `http://localhost/dashboard` no seu navegador web para visualizar as métricas em tempo real do sistema de estacionamento.
* **Gerar PDF do Histórico:**
    * No Dashboard, clique no botão "Gerar PDF do Histórico de Estacionamento" para baixar um relatório em PDF com os detalhes das operações de estacionamento.

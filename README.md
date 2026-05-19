# Mini CRM de Contatos

Uma API REST completa para gerenciamento de contatos, com processamento assíncrono de *score* (pontuação) e atualizações em tempo real via WebSockets.

---

## Arquitetura e Decisões Técnicas

Este projeto foi construído fugindo do padrão clássico (MVC "Fat Controller / Fat Model"), adotando conceitos sólidos de **Domain-Driven Design (DDD)**, **Arquitetura Limpa** e princípios **SOLID**.

*   **Camada de Domínio**: Rica e agnóstica de framework.
    *   **Value Objects**: `Email` e `Phone` validam regras e formatos próprios na inicialização.
    *   **Domain Services**: `CalculateContactScoreService` encapsula matematicamente a lógica de negócio do cálculo de pontuação isolada do banco.
*   **Camada de Aplicação**:
    *   **Use Cases**: O `CreateContactUseCase` orquestra o fluxo de negócio de entrada isolando o Controller de regras profundas.
*   **Camada de Infraestrutura**:
    *   **Repository Pattern**: O `ContactEloquentRepository` implementa a interface requerida pelo *Use Case*, garantindo a Inversão de Dependência (IoC) injetada via `AppServiceProvider`.
    *   **Laravel Observers**: Utilizado no salvamento de contatos para normalizar magicamente números de telefone (remover parênteses e traços).
    *   **Jobs e Eventos**: O processamento assíncrono ocorre no `ProcessContactScoreJob`, que por sua vez dispara o evento `ContactScoreProcessed` repassando pro WebSocket via **Laravel Reverb**.
    *   **Listeners**: O `LogContactScoreProcessedListener` garante o log físico do resultado no arquivo customizado `storage/logs/contact.log`.

## Tecnologias Utilizadas

*   **PHP 8.2+** / **Laravel 11**
*   **Docker** (Laravel Sail)
*   **Laravel Reverb** (WebSockets)
*   **Redis / DB** (Background Jobs)
*   **PHPUnit** (Test-Driven Development)

---

## Guia de Setup e Execução

O projeto está totalmente dockerizado. Para começar a rodar ou testar após clonar o repositório do GitHub, siga o passo a passo abaixo.

### 1. Preparando as Dependências
Como o repositório vem limpo do GitHub, você precisa instalar as dependências do Composer.

*   **Se você tiver PHP e Composer locais:**
    ```bash
    composer install
    cp .env.example .env
    ```
*   **Se você NÃO tiver PHP local (usando apenas o Docker):**
    Rode o comando abaixo no terminal para instalar as dependências usando uma imagem docker temporária do Composer:
    ```bash
    docker run --rm --interactive --tty -v ${PWD}:/app composer install
    cp .env.example .env
    ```

### 2. Inicializando o Ambiente (Setup Completo)

Você pode inicializar o projeto de duas formas, dependendo do seu ambiente local:

#### Opção A: Setup Completo via Docker
Se você não tem o PHP/SQLite configurados localmente ou quer evitar erros de extensões ausentes (ex: `pdo_sqlite`), use o comando integrado ao Docker:
```bash
composer docker:setup
```
*(Esse comando baixa as dependências, cria os arquivos necessários e inicializa os contêineres executando o `key:generate` e `migrate` de forma 100% segura dentro do ambiente Docker virtualizado).*

#### Opção B: Setup Local (Requer PHP + Extensões locais)
Se você possui o PHP e o driver SQLite localmente configurados em sua máquina, pode rodar o setup tradicional:
```bash
composer setup
```
*(Esse comando copia o `.env`, cria o banco SQLite local, gera a chave de criptografia, roda as migrações locais e compila o frontend com Vite).*

### 3. Subindo e Gerenciando a Infraestrutura Docker
Uma vez que o projeto esteja inicializado, você pode gerenciar os contêineres Docker utilizando os seguintes comandos integrados no Composer:

*   **Subir infraestrutura (MySQL, Redis, Laravel Reverb):**
    ```bash
    composer docker:up
    ```
*   **Derrubar infraestrutura:**
    ```bash
    composer docker:down
    ```
*   **Rodar migrações dentro do contêiner Docker:**
    ```bash
    composer docker:migrate
    ```

---

## Suíte de Testes (TDD)

A aplicação foi criada sob a filosofia TDD (Test-Driven Development) e possui testes de unidade e de integração completos.

> [!NOTE]  
> **Lógica de Testes Aprimorada:** O projeto foi configurado com um banco de dados SQLite **in-memory (`:memory:`)** e uma chave `APP_KEY` estática em `phpunit.xml`. Isso significa que a suíte de testes funcionará de forma robusta e isolada imediatamente após a clonagem, sem necessidade de banco físico ou configurações manuais!

Você pode executar os testes de duas formas:

### Opção A: Utilizando Docker (Recomendado e Sem Setup Local)
A forma mais robusta e independente de plataforma para rodar os testes é executando-os diretamente dentro do contêiner Docker do projeto:
```bash
composer test:docker
```
*(Esse atalho compila/executa os testes em um ambiente virtualizado isolado, resolvendo quaisquer divergências de sistema operacional, incluindo Windows, macOS e Linux).*

### Opção B: Executando Localmente (Requer PHP + Extensão pdo_sqlite)
Se você tiver o PHP com o driver SQLite habilitado na sua máquina, basta rodar:
```bash
composer test
```
ou
```bash
php artisan test
```

---

## Como Visualizar e Acessar o Projeto (CRUD & API)

A aplicação possui um **Web Client (Interface Gráfica)** funcional e reativo (com integração via WebSocket/Laravel Reverb) servido na raiz do projeto, além de uma API completa.

### 1. Interface Web (CRUD no Navegador)
Acesse a página inicial para interagir com o formulário de contatos e ver as atualizações em tempo real:
*   **Via Docker (Sail):** `http://localhost`
*   **Via Servidor Local:** `http://localhost:8000`

### 2. Endpoints da API (URL Base)
Caso queira integrar com outros serviços ou testar programaticamente, a URL base da API é:
*   **Via Docker (Sail):** `http://localhost/api/contacts`
*   **Via Servidor Local:** `http://localhost:8000/api/contacts`

---

## Endpoints da API

Caso queira testar a API via Postman/Insomnia, estas são as rotas disponíveis (todas exigem/retornam cabeçalhos `Accept: application/json`):

*   **Listar contatos (Paginado):** `GET /api/contacts`
*   **Criar novo contato:** `POST /api/contacts`
*   **Visualizar contato específico:** `GET /api/contacts/{id}`
*   **Atualizar contato:** `PUT /api/contacts/{id}`
*   **Remover contato (Soft Delete):** `DELETE /api/contacts/{id}`
*   **Processar pontuação (Fila):** `POST /api/contacts/{id}/process-score` (Envia a ordem de pontuação para processamento assíncrono).

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
    *   **Jobs e Eventos**: O processamento de score ocorre no `ProcessContactScoreJob`, que por sua vez dispara o evento `ContactScoreProcessed` repassando pro WebSocket via **Laravel Reverb**.
    *   **Listeners**: O `LogContactScoreProcessedListener` garante o log físico do resultado no arquivo customizado `storage/logs/contact.log`.

## Tecnologias Utilizadas

*   **PHP 8.2+** / **Laravel 11**
*   **Docker** (Laravel Sail)
*   **Laravel Reverb** (WebSockets)
*   **Redis** (Background Jobs)
*   **PHPUnit** (Test-Driven Development)

---

## Guia de Setup e Execução (Docker)

O projeto está totalmente dockerizado para garantir consistência de ambiente e evitar conflitos de drivers ou extensões locais (como banco de dados ou WebSockets). Siga o passo a passo abaixo para rodar e testar.

### 1. Instalando as Dependências do Composer via Docker
Como o repositório do GitHub vem limpo, rode o comando abaixo no seu terminal para baixar as dependências utilizando uma imagem temporária do Composer e preparar o arquivo `.env`:

```bash
docker run --rm --interactive --tty -v ${PWD}:/app composer install
cp .env.example .env
```

### 2. Inicializando o Ambiente (Setup Completo)
Com o Docker aberto em sua máquina, execute o comando de setup automatizado:

```bash
composer docker:setup
```
*(Este comando cria os contêineres do Docker, gera as chaves criptográficas necessárias e roda as migrações do banco de dados de forma 100% segura e isolada dentro do ambiente virtualizado).*

### 3. Comandos Úteis de Gerenciamento
Após a inicialização inicial, você pode usar os seguintes atalhos do Composer para gerenciar seus contêineres:

*   **Subir a infraestrutura:**
    ```bash
    composer docker:up
    ```
*   **Derrubar os contêineres:**
    ```bash
    composer docker:down
    ```
*   **Executar migrações do banco (caso necessário):**
    ```bash
    composer docker:migrate
    ```

---

## Suíte de Testes (TDD)

A aplicação foi criada sob a filosofia TDD (Test-Driven Development) e possui testes de unidade e de integração completos.

> [!NOTE]  
> **Lógica de Testes Aprimorada:** O projeto foi configurado com um banco de dados SQLite **in-memory (`:memory:`)** e uma chave `APP_KEY` estática em `phpunit.xml`. Isso significa que a suíte de testes funcionará de forma robusta e isolada imediatamente após a clonagem, sem necessidade de banco físico ou configurações manuais!

Para executar a suíte de testes de forma isolada dentro do contêiner Docker, execute:

```bash
composer test:docker
```
*(Esse comando roda todos os testes no ambiente Docker idêntico ao de produção, garantindo 100% de sucesso independente da plataforma do avaliador).*

---

## Como Acessar o Projeto (CRUD & API)

A aplicação possui um **Web Client (Interface Gráfica)** funcional e reativo servido na raiz do projeto, além de uma API completa.

### 1. Interface Web (CRUD no Navegador)
Acesse a URL abaixo no seu navegador para interagir com o formulário de gerenciamento de contatos:
*   **Endereço:** [http://localhost](http://localhost)

### 2. Endpoints da API (URL Base)
Caso queira testar a API via Postman/Insomnia ou fazer requisições externas, a URL base é:
*   **Endereço:** [http://localhost/api/contacts](http://localhost/api/contacts)

---

## Endpoints Disponíveis da API

Caso queira testar os endpoints programaticamente (todas as requisições exigem o cabeçalho `Accept: application/json`):

*   **Listar contatos (Paginado):** `GET /api/contacts`
*   **Criar novo contato:** `POST /api/contacts`
*   **Visualizar contato específico:** `GET /api/contacts/{id}`
*   **Atualizar contato:** `PUT /api/contacts/{id}`
*   **Remover contato (Soft Delete):** `DELETE /api/contacts/{id}`
*   **Processar pontuação:** `POST /api/contacts/{id}/process-score` (Dispara o cálculo síncrono e imediato do score e status do contato).

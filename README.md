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

O projeto está totalmente dockerizado. Você precisará ter o [Docker](https://docs.docker.com/engine/install/) rodando.

### 1. Preparando as Dependências
Como o repositório é "limpo", instale as dependências via Composer para baixar o Laravel Sail. Se você tiver PHP local:
```bash
composer install
cp .env.example .env
```
*(Se não possuir PHP local, use um container efêmero para gerar o vendor, veja a [documentação do Laravel](https://laravel.com/docs/11.x/sail#installing-composer-dependencies-for-existing-projects)).*

### 2. Subindo a Infraestrutura
Levante todos os contêineres necessários (App, Banco de Dados):
```bash
./vendor/bin/sail up -d

# Gere a chave da aplicação
./vendor/bin/sail artisan key:generate
```

### 3. Executando as Migrações
Com o banco rodando, crie a estrutura de tabelas:
```bash
./vendor/bin/sail artisan migrate
```

### 4. Processamento Assíncrono e WebSockets
O cálculo de *score* foi construído em *background* e empurra a atualização para o frontend via *broadcasting*. Para isso, você precisará de dois processos rodando em paralelo no terminal:

**Terminal A (Worker das Filas):**
```bash
./vendor/bin/sail artisan queue:work
```

**Terminal B (Servidor Reverb - WebSockets):**
```bash
./vendor/bin/sail artisan reverb:start
```

### 5. Acessando a Aplicação
Abra o navegador em: **[http://localhost](http://localhost)**
Você verá um SPA simples feito puramente em HTML/JS com Laravel Echo escutando eventos. Adicione um contato e observe a mudança de status e score ocorrer em tempo real (ficará verde)!

---

## Suíte de Testes (TDD)

A aplicação foi criada sob a filosofia TDD (Test-Driven Development).
Possuímos uma mescla de **Testes de Unidade** (validando Domínio isolado com *mocks*) e **Testes de Integração/Feature** (consumindo toda a API).

Para atestar o funcionamento executando a suíte inteira, rode:
```bash
./vendor/bin/sail artisan test
```

---

## Endpoints da API

Caso queira testar a API via Postman/Insomnia, estas são as rotas disponíveis (Todas respondem em JSON):

*   `POST /api/contacts` - Criação de novo contato
*   `GET /api/contacts` - Lista paginada
*   `GET /api/contacts/{id}` - Visualiza um contato específico
*   `PUT /api/contacts/{id}` - Atualiza um contato existente
*   `DELETE /api/contacts/{id}` - Deleta um contato (Soft Delete)
*   `POST /api/contacts/{id}/process-score` - Gatilho que envia a ordem de processamento de pontuação para a Fila.

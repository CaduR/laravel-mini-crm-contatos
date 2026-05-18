## Como Executar o Projeto

Este projeto foi desenvolvido utilizando Docker. Siga os passos abaixo para rodar a aplicação localmente:

### 1. Subir os contêineres (Docker)
Inicie o ambiente utilizando o Laravel Sail ou o Docker Compose diretamente:
```bash
# Usando o Laravel Sail
./vendor/bin/sail up -d

# Ou usando Docker Compose direto
docker compose up -d
```

### 2. Rodar as Migrations
Com os contêineres rodando, crie as tabelas no banco de dados:
```bash
./vendor/bin/sail artisan migrate
```

### 3. Iniciar as Filas (Processamento Assíncrono)
Para que o cálculo de *score* funcione corretamente em segundo plano, abra um terminal e rode o worker do Laravel:
```bash
./vendor/bin/sail artisan queue:work
```

### 4. Iniciar o WebSocket (Laravel Reverb)
Para que as atualizações em tempo real apareçam na tela, abra outro terminal e inicie o Reverb:
```bash
./vendor/bin/sail artisan reverb:start
```

### 5. Acessar a Aplicação
Abra o navegador em `http://localhost`. A tela exibirá um CRUD funcional puro (sem estilização complexa, focado na funcionalidade real) onde você pode adicionar contatos e ver a fila processando a pontuação em tempo real.

---

## Como Rodar os Testes

Para executar a suíte completa de testes (Testes Unitários da camada de Domínio/Aplicação e Testes de Integração da API), rode o comando abaixo:

```bash
./vendor/bin/sail artisan test
```

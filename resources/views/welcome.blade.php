<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Mini CRM</title>
    <!-- scripts obrigatórios do WebSocket -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
</head>

<body>
    <h1>Mini CRM</h1>
    <div id="ws-status" style="color: orange;">Conectando WebSocket</div>

    <hr>

    <!-- FORMULÁRIO -->
    <h2>Criar Novo Contato</h2>
    <form id="contact-form">
        <label>Nome: <input type="text" id="name" required></label><br><br>
        <label>E-mail: <input type="email" id="email" required></label><br><br>
        <label>Telefone: <input type="text" id="phone" required></label><br><br>
        <button type="submit">Salvar</button>
    </form>

    <hr>

    <!-- LISTA DE CONTATOS -->
    <h2>Contatos Salvos</h2>
    <ul id="contacts-list">
        <li>Carregando da API...</li>
    </ul>

    <script>
        // Conexão com websocket e reverb
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env("REVERB_APP_KEY") }}',
            wsHost: window.location.hostname,
            wsPort: 8080,
            wssPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });

        document.getElementById('ws-status');
        window.Echo.connector.pusher.connection.bind('connected', () => {
            document.getElementById('ws-status').innerText = "WebSocket Conectado";
            document.getElementById('ws-status').style.color = "green";
        });

        // Escuta o evento do Back-end
        window.Echo.channel('contacts')
            .listen('ContactScoreProcessed', (e) => {
                const li = document.getElementById('contact-' + e.contact.id);
                if (li) {
                    li.innerHTML = renderContact(e.contact);
                    li.style.backgroundColor = 'lightgreen'; // Destaca quando atualiza
                    setTimeout(() => li.style.backgroundColor = 'transparent', 1000);
                } else {
                    loadContacts();
                }
            });

        // Funções da API
        const contactsList = document.getElementById('contacts-list');
        const form = document.getElementById('contact-form');

        // Como um contato é exibido na lista
        function renderContact(contact) {
            return `
                <b>${contact.name}</b> (${contact.email} | Tel: ${contact.phone}) <br>
                Status: [${contact.status}] | Score: ${contact.score || '??'} 
                <button onclick="deleteContact(${contact.id})" style="color: red; margin-left: 10px; cursor: pointer;">Deletar</button>
                <hr>
            `;
        }

        // Função para Deletar um contato (Chama a rota DELETE da nossa API)
        async function deleteContact(id) {
            if(confirm('Tem certeza que deseja apagar este contato?')) {
                const res = await fetch(`/api/contacts/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (res.ok) {
                    loadContacts(); // Recarrega a lista para mostrar que sumiu
                } else {
                    alert('Erro ao apagar contato.');
                }
            }
        }

        // Busca (GET) todos os contatos
        async function loadContacts() {
            const res = await fetch('/api/contacts', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            contactsList.innerHTML = '';

            data.data.forEach(contact => {
                const li = document.createElement('li');
                li.id = 'contact-' + contact.id;
                li.innerHTML = renderContact(contact);
                contactsList.appendChild(li);
            });
        }

        loadContacts();

        // Envia (POST) um novo contato
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('button');
            btn.disabled = true;

            const payload = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
            };

            // Passo A: Cria no banco
            const resCreate = await fetch('/api/contacts', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            const created = await resCreate.json();

            if (resCreate.ok) {
                // Passo B: Pede pro back-end calcular o score (manda pra Fila)
                await fetch(`/api/contacts/${created.data.id}/process-score`, { method: 'POST' });

                form.reset();
                loadContacts(); // Recarrega a lista para mostrar ele como 'pending'
            } else {
                alert("Erro: " + JSON.stringify(created));
            }

            btn.disabled = false;
        });
    </script>
</body>

</html>
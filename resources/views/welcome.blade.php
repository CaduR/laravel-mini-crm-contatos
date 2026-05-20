<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini CRM - Gestão de Contatos</title>
    <!-- WebSocket Reverb Dependencies -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <style>
        :root {
            --bg-app: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --success: #16a34a;
            --danger: #dc2626;
            --radius: 8px;
            --transition: all 0.2s ease-in-out;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-app);
            color: var(--text-main);
            line-height: 1.5;
            padding: 2rem 1.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.25rem;
        }

        header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .ws-badge {
            font-size: 0.825rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #fff;
            border: 1px solid var(--border-color);
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .ws-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            background-color: #cbd5e1;
        }

        .ws-connected .ws-indicator {
            background-color: var(--success);
        }

        .ws-connecting .ws-indicator {
            background-color: #f59e0b;
        }

        .layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
            align-items: start;
        }

        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .card h2 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--text-main);
            letter-spacing: -0.02em;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.375rem;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: #fff;
            color: var(--text-main);
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary);
            color: #fff;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #fff;
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background-color: #f8fafc;
        }

        .btn-danger {
            background-color: #fff;
            border-color: #fee2e2;
            color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #fef2f2;
            border-color: #fca5a5;
        }

        .btn-danger-confirm {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger-confirm:hover {
            background-color: #b91c1c;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 4px;
        }

        .table-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: #334155;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #b45309;
        }

        .badge-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-failed {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #f8fafc;
            border-top: 1px solid var(--border-color);
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        .pagination-buttons {
            display: flex;
            gap: 0.5rem;
        }

        /* Modal minimalista */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(1px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.15s ease-out, visibility 0.15s ease-out;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 440px;
            padding: 1.5rem;
            transform: translateY(8px);
            transition: transform 0.15s ease-out;
        }

        .modal-overlay.active .modal-card {
            transform: translateY(0);
        }

        .modal-card h2 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .error-message {
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: 0.375rem;
            display: none;
            line-height: 1.25;
        }

        .actions-cell {
            display: flex;
            gap: 0.375rem;
        }

        @keyframes flashUpdate {
            0% {
                background-color: #d1fae5;
            }

            100% {
                background-color: transparent;
            }
        }

        .flash-update td {
            animation: flashUpdate 1.5s ease-out forwards;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>Mini CRM</h1>
            <div class="ws-badge" id="ws-badge-container" style="display: none;">
                <span class="ws-indicator" id="ws-indicator"></span>
                <span id="ws-status-text">Conectando...</span>
            </div>
        </header>

        <div class="layout">
            <!-- COLUNA ESQUERDA: FORMULÁRIO DE CADASTRO -->
            <div class="card">
                <h2>Criar Novo Contato</h2>
                <form id="contact-form">
                    <div class="form-group">
                        <label for="name">Nome completo</label>
                        <input type="text" id="name" class="form-control" placeholder="Ex: João Silva" required>
                        <div id="create-error-name" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" class="form-control" placeholder="joao@exemplo.com" required>
                        <div id="create-error-email" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="text" id="phone" class="form-control" placeholder="Ex: (11) 99999-9999" required>
                        <div id="create-error-phone" class="error-message"></div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btn-submit-create">Salvar Contato</button>
                    <div id="create-error-general" class="error-message" style="margin-top: 0.75rem;"></div>
                </form>
            </div>

            <!-- COLUNA DIREITA: LISTAGEM DE CONTATOS -->
            <div>
                <div class="table-wrapper">
                    <table id="contacts-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Nome</th>
                                <th style="width: 25%;">E-mail</th>
                                <th style="width: 20%;">Telefone</th>
                                <th style="width: 12%;">Status</th>
                                <th style="width: 8%;">Score</th>
                                <th style="width: 10%;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="contacts-list">
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    Carregando contatos...
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Controles de Paginação -->
                    <div class="pagination">
                        <span id="pagination-info">Carregando paginação...</span>
                        <div class="pagination-buttons">
                            <button class="btn btn-secondary btn-sm" id="btn-prev" onclick="goToPage(currentPage - 1)" disabled>Anterior</button>
                            <button class="btn btn-secondary btn-sm" id="btn-next" onclick="goToPage(currentPage + 1)" disabled>Próxima</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE EDIÇÃO -->
    <div id="edit-modal" class="modal-overlay">
        <div class="modal-card">
            <h2>Editar Contato</h2>
            <form id="edit-form">
                <input type="hidden" id="edit-id">

                <div class="form-group">
                    <label for="edit-name">Nome completo</label>
                    <input type="text" id="edit-name" class="form-control" required>
                    <div id="edit-error-name" class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="edit-email">E-mail</label>
                    <input type="email" id="edit-email" class="form-control" required>
                    <div id="edit-error-email" class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="edit-phone">Telefone</label>
                    <input type="text" id="edit-phone" class="form-control" required>
                    <div id="edit-error-phone" class="error-message"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('edit-modal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="width: auto;" id="btn-submit-edit">Salvar Alterações</button>
                </div>
                <div id="edit-error-general" class="error-message" style="margin-top: 0.75rem;"></div>
            </form>
        </div>
    </div>

    <!-- MODAL DE CONFIRMAÇÃO DE DELEÇÃO -->
    <div id="delete-modal" class="modal-overlay">
        <div class="modal-card">
            <h2>Confirmar Exclusão</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                Tem certeza que deseja apagar o contato <strong id="delete-contact-name"></strong>? Esta ação realiza uma exclusão lógica (soft delete).
            </p>
            <input type="hidden" id="delete-id">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('delete-modal')">Cancelar</button>
                <button type="button" class="btn btn-danger-confirm btn" id="btn-confirm-delete" onclick="executeDelete()">Excluir Contato</button>
            </div>
        </div>
    </div>

    <script>
        // CONFIGURAÇÕES DO WEBSOCKET E REVERB
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

        const wsBadgeContainer = document.getElementById('ws-badge-container');
        const wsStatusText = document.getElementById('ws-status-text');

        window.Echo.connector.pusher.connection.bind('connected', () => {
            wsBadgeContainer.className = "ws-badge ws-connected";
            wsStatusText.innerText = "WebSocket Ativo";
            wsBadgeContainer.style.display = 'flex';
        });

        window.Echo.connector.pusher.connection.bind('connecting', () => {
            wsBadgeContainer.className = "ws-badge ws-connecting";
            wsStatusText.innerText = "Conectando...";
            wsBadgeContainer.style.display = 'none';
        });

        window.Echo.connector.pusher.connection.bind('unavailable', () => {
            wsBadgeContainer.style.display = 'none';
        });

        window.Echo.connector.pusher.connection.bind('failed', () => {
            wsBadgeContainer.style.display = 'none';
        });

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            wsBadgeContainer.style.display = 'none';
        });

        // ESTADO GLOBAL DO FRONT-END
        let currentPage = 1;
        let lastPage = 1;
        let currentContactsList = [];
        const subscribedChannels = new Set();

        const contactsListContainer = document.getElementById('contacts-list');
        const createForm = document.getElementById('contact-form');
        const editForm = document.getElementById('edit-form');

        // ESCUTA EVENTOS VIA LARAVEL ECHO / REVERB
        function listenToContact(contactId) {
            if (subscribedChannels.has(contactId)) return;

            subscribedChannels.add(contactId);
            window.Echo.channel('contacts.' + contactId)
                .listen('ContactScoreProcessed', (e) => {
                    // Encontra e atualiza o contato na lista local
                    const index = currentContactsList.findIndex(c => c.id === e.contact.id);
                    if (index !== -1) {
                        currentContactsList[index] = e.contact;
                    }
                    
                    // Atualiza a linha correspondente no HTML
                    const tr = document.getElementById('contact-' + e.contact.id);
                    if (tr) {
                        tr.innerHTML = getContactRowHTML(e.contact);
                        tr.classList.add('flash-update');
                        setTimeout(() => tr.classList.remove('flash-update'), 1500);
                    }
                });
        }

        // RETORNA A ESTRUTURA HTML DE UMA LINHA DA TABELA
        function getContactRowHTML(contact) {
            const statusLabels = {
                'pending': 'Pendente',
                'processing': 'Processando',
                'active': 'Ativo',
                'failed': 'Falhou'
            };
            const statusText = statusLabels[contact.status] || contact.status;
            const scoreVal = (contact.score !== null && contact.score !== undefined) ? contact.score : '—';

            return `
                <td><strong>${escapeHTML(contact.name)}</strong></td>
                <td>${escapeHTML(contact.email)}</td>
                <td>${escapeHTML(contact.phone)}</td>
                <td><span class="badge badge-${contact.status}">${statusText}</span></td>
                <td><strong>${scoreVal}</strong></td>
                <td>
                    <div class="actions-cell">
                        <button class="btn btn-secondary btn-sm" onclick="openEditModal(${contact.id})">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(${contact.id}, '${escapeJS(contact.name)}')">Deletar</button>
                    </div>
                </td>
            `;
        }

        // CARREGA E RENDERIZA OS CONTATOS DA API
        async function loadContacts(page = 1) {
            try {
                const res = await fetch(`/api/contacts?page=${page}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!res.ok) throw new Error("Erro ao consultar a API.");

                const response = await res.json();
                currentContactsList = response.data;
                
                contactsListContainer.innerHTML = '';

                if (currentContactsList.length === 0) {
                    contactsListContainer.innerHTML = `
                        <tr>
                            <td colspan="6" class="empty-state">Nenhum contato encontrado nesta página.</td>
                        </tr>
                    `;
                } else {
                    currentContactsList.forEach(contact => {
                        const tr = document.createElement('tr');
                        tr.id = 'contact-' + contact.id;
                        tr.innerHTML = getContactRowHTML(contact);
                        contactsListContainer.appendChild(tr);

                        // Inscreve no WebSocket para atualizações em tempo real
                        listenToContact(contact.id);
                    });
                }

                // Atualiza dados e botões de paginação
                currentPage = response.meta.current_page;
                lastPage = response.meta.last_page;
                updatePaginationUI(response.meta);

            } catch (err) {
                contactsListContainer.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--danger); padding: 2rem;">
                            Erro ao carregar contatos do servidor.
                        </td>
                    </tr>
                `;
            }
        }

        // NAVEGAÇÃO DE PÁGINA
        function goToPage(page) {
            if (page >= 1 && page <= lastPage) {
                loadContacts(page);
            }
        }

        // ATUALIZA PAGINAÇÃO
        function updatePaginationUI(meta) {
            const info = document.getElementById('pagination-info');
            info.innerText = `Página ${meta.current_page} de ${meta.last_page} | Total: ${meta.total} contatos`;

            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');

            btnPrev.disabled = meta.current_page === 1;
            btnNext.disabled = meta.current_page === meta.last_page || meta.last_page === 0;
        }

        // CRIAR CONTATO (SUBMIT DO FORMULÁRIO PRINCIPAL)
        createForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors('create');

            const btn = document.getElementById('btn-submit-create');
            btn.disabled = true;

            const payload = {
                name: document.getElementById('name').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
            };

            try {
                // Passo 1: Cria o contato na API
                const resCreate = await fetch('/api/contacts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await resCreate.json();

                if (resCreate.ok) {
                    // Passo 2: Dispara o cálculo do score em segundo plano
                    await fetch(`/api/contacts/${data.data.id}/process-score`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' }
                    });

                    createForm.reset();
                    loadContacts(1); // Vai para a primeira página ver o novo contato 'pending'
                } else if (resCreate.status === 422) {
                    showValidationErrors('create', data.errors);
                } else {
                    showGeneralError('create', data.message || "Erro desconhecido ao salvar contato.");
                }
            } catch (err) {
                showGeneralError('create', "Falha de rede ao se comunicar com a API.");
            } finally {
                btn.disabled = false;
            }
        });

        // EDICÃO: ABRE MODAL E PREENCHE CAMPOS
        function openEditModal(contactId) {
            clearErrors('edit');
            const contact = currentContactsList.find(c => c.id === contactId);
            if (!contact) return;

            document.getElementById('edit-id').value = contact.id;
            document.getElementById('edit-name').value = contact.name;
            document.getElementById('edit-email').value = contact.email;
            document.getElementById('edit-phone').value = contact.phone;

            openModal('edit-modal');
        }

        // EDIÇÃO: SUBMIT DO FORMULÁRIO DE EDIÇÃO
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors('edit');

            const btn = document.getElementById('btn-submit-edit');
            btn.disabled = true;

            const id = document.getElementById('edit-id').value;
            const payload = {
                name: document.getElementById('edit-name').value.trim(),
                email: document.getElementById('edit-email').value.trim(),
                phone: document.getElementById('edit-phone').value.trim(),
            };

            try {
                const resUpdate = await fetch(`/api/contacts/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await resUpdate.json();

                if (resUpdate.ok) {
                    closeModal('edit-modal');
                    loadContacts(currentPage); // Recarrega a página atual para ver as edições
                } else if (resUpdate.status === 422) {
                    showValidationErrors('edit', data.errors);
                } else {
                    showGeneralError('edit', data.message || "Erro desconhecido ao atualizar contato.");
                }
            } catch (err) {
                showGeneralError('edit', "Falha de rede ao se comunicar com a API.");
            } finally {
                btn.disabled = false;
            }
        });

        // EXCLUSÃO: ABRE MODAL DE CONFIRMAÇÃO
        function confirmDelete(id, name) {
            document.getElementById('delete-id').value = id;
            document.getElementById('delete-contact-name').innerText = name;
            openModal('delete-modal');
        }

        // EXCLUSÃO: EXECUTA O DELETE DE FATO
        async function executeDelete() {
            const id = document.getElementById('delete-id').value;
            const btn = document.getElementById('btn-confirm-delete');
            btn.disabled = true;

            try {
                const res = await fetch(`/api/contacts/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (res.ok) {
                    closeModal('delete-modal');
                    // Se era o único item da página (e não for a página 1), volta uma página
                    if (currentContactsList.length === 1 && currentPage > 1) {
                        loadContacts(currentPage - 1);
                    } else {
                        loadContacts(currentPage);
                    }
                } else {
                    alert('Erro ao apagar contato.');
                }
            } catch (err) {
                alert('Erro de conexão ao apagar contato.');
            } finally {
                btn.disabled = false;
            }
        }

        // AUXILIARES DE MODAL
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // AUXILIARES DE TRATAMENTO DE ERROS DE VALIDAÇÃO
        function clearErrors(prefix) {
            document.getElementById(`${prefix}-error-name`).style.display = 'none';
            document.getElementById(`${prefix}-error-email`).style.display = 'none';
            document.getElementById(`${prefix}-error-phone`).style.display = 'none';
            document.getElementById(`${prefix}-error-general`).style.display = 'none';
        }

        function showValidationErrors(prefix, errors) {
            if (errors.name) {
                const el = document.getElementById(`${prefix}-error-name`);
                el.innerText = errors.name.join(' ');
                el.style.display = 'block';
            }
            if (errors.email) {
                const el = document.getElementById(`${prefix}-error-email`);
                el.innerText = errors.email.join(' ');
                el.style.display = 'block';
            }
            if (errors.phone) {
                const el = document.getElementById(`${prefix}-error-phone`);
                el.innerText = errors.phone.join(' ');
                el.style.display = 'block';
            }
        }

        function showGeneralError(prefix, msg) {
            const el = document.getElementById(`${prefix}-error-general`);
            el.innerText = msg;
            el.style.display = 'block';
        }

        // AUXILIARES CONTRA XSS / STRING ESCAPE
        function escapeHTML(str) {
            if (!str) return '';
            return str.replace(/[&<>'"]/g,
                tag => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#39;',
                    '"': '&quot;'
                }[tag] || tag)
            );
        }

        function escapeJS(str) {
            if (!str) return '';
            return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
        }

        // CARGA INICIAL
        loadContacts(1);
    </script>
</body>

</html>
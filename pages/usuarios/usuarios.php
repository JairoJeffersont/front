<div class="d-flex" id="wrapper">
    <?php include 'pages/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include 'pages/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>

            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-people-fill"></i> Adicionar usuários</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Todos os campos são obrigatórios</p>
                    <p class="card-text mb-0" id="assinaturas"></p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" maxlength="15" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" placeholder="Aniversário (dd/mm)" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="usuario_ativo" required>
                                <option value="true">Ativado</option>
                                <option value="false" selected>Desativado</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="password" class="form-control form-control-sm" id="usuario_senha" name="usuario_senha" placeholder="Senha" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="password" class="form-control form-control-sm" id="usuario_senha2" name="usuario_senha2" placeholder="Confirme a senha" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="file-upload">
                                <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div id="alerta"></div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Telefone</th>
                                    <th scope="col">Ativo</th>
                                </tr>
                            </thead>
                            <tbody id="tabela">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalLoading" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img class="rounded mx-auto d-block" width="200" src="img/loading.gif">
            </div>
        </div>
    </div>
</div>


<script>
    window.onload = function() {
        montarTabela();
        mostrarCliente()
        inserirUsuario();
    };


    async function mostrarCliente() {
        try {
            const url = `${apiBaseUrl}/clientes?/${localStorage.getItem('cliente_id')}`;
            const method = 'get';

            const assinaturas = document.getElementById('assinaturas');

            const response = await requestApi(url, method, null, localStorage.getItem('usuario_token'));

            assinaturas.innerHTML = `Quantidade de usuários permitidos na assinatura: <b>${response.data.dados[0].cliente_assinaturas}</b>`;

        } catch (e) {
            if (e.error.status == 404 || e.error.status == 500) {
                showAlert('danger', e.error.message, 3000);
            }
        }
    }

    async function inserirUsuario() {

        const form = document.getElementById('form_novo');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            showModal();

            const formData = new FormData(form);

            const dados = {};
            formData.forEach((value, key) => {
                dados[key] = value;
            });

            if (dados['usuario_senha'].length < 6) {
                showAlert('danger', 'A senha deve ter pelo menos 6 caracteres', 3000);
                return false;
            }

            if (dados['usuario_senha'] != dados['usuario_senha2']) {
                showAlert('danger', 'Senhas não conferem', 3000);
                return false;
            }


            dados['usuario_cliente'] = localStorage.getItem('cliente_id');

            delete dados['usuario_senha2'];

            try {
                const url = `${apiBaseUrl}/usuarios`;
                const method = 'POST';

                const response = await requestApi(url, method, dados, localStorage.getItem('usuario_token'));

                showAlert('success', response.data.message, 3000);
                form.reset();
                montarTabela();

            } catch (e) {
                hideModal();
                if (e.error.status == 409) {
                    showAlert('info', e.error.message, 3000);
                }

                if (e.error.status == 404 || e.error.status == 400 || e.error.status == 500) {
                    showAlert('danger', e.error.message, 3000);
                }
            }
        });
    }

    async function montarTabela() {
        showModal();
        try {
            const url = `${apiBaseUrl}/usuarios?cliente_id=${localStorage.getItem('cliente_id')}`;
            const method = 'get';

            let tabela = document.getElementById("tabela");

            const response = await requestApi(url, method, null, localStorage.getItem('usuario_token'));

            if (response.data.status == 200) {
                tabela.innerHTML = "";
                for (const usuario of response.data.dados) {
                    const linha = `
                                <tr>
                                    <td style="white-space: nowrap;"><a href="?secao=usuario&id=${usuario.usuario_id}">${usuario.usuario_nome}</a></td>
                                    <td style="white-space: nowrap;">${usuario.usuario_email}</td>
                                    <td style="white-space: nowrap;">${usuario.usuario_aniversario}</td>
                                    <td style="white-space: nowrap;">${usuario.usuario_telefone}</td>
                                    <td style="white-space: nowrap;">${usuario.usuario_ativo ? 'Ativo' : 'Desativado'}</td>
                                </tr>
                            `;
                    tabela.insertAdjacentHTML("beforeend", linha);
                }
            }

            if (response.data.status == 204) {
                tabela.innerHTML = "";
                const linha = `<tr><td colspan="5">${response.data.message}</td></tr>`;
                tabela.insertAdjacentHTML("beforeend", linha);
            }
            hideModal();

        } catch (e) {
            if (e.error.status == 404 || e.error.status == 500) {
                showAlert('danger', e.error.message, 3000);
            }
        }
    }
</script>
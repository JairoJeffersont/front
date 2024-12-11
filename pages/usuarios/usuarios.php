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
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
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
                                <option value="1">Ativado</option>
                                <option value="0" selected>Desativado</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="usuario_nivel" required>
                                <option value="1">Administrador</option>
                                <option value="2" selected>Assessor</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="password" class="form-control form-control-sm" id="usuario_senha" name="usuario_senha" placeholder="Senha" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="password" class="form-control form-control-sm" id="usuario_senha2" name="usuario_senha2" placeholder="Confirme a senha" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="file" class="form-control form-control-sm" name="usuario_foto"  required>
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
    window.onload = () => {
        montarTabela();
        configurarFormulario();
    };

    async function montarTabela() {
        try {
            showModal();
            const {data} = await requestApi(`${apiBaseUrl}/?rota=usuarios`, 'GET');

            document.getElementById("tabela").innerHTML = data.status === 'success' ?
                data.dados.map(usuario => `
                    <tr>
                        <td><a href="?secao=usuario&id=${usuario.usuario_id}">${usuario.usuario_nome}</a></td>
                        <td>${usuario.usuario_email}</td>
                        <td>${usuario.usuario_aniversario}</td>
                        <td>${usuario.usuario_telefone}</td>
                        <td>${usuario.usuario_ativo ? 'Ativo' : 'Desativado'}</td>
                    </tr>`).join('') :
                `<tr><td colspan="5">${data.message}</td></tr>`;

        } catch (e) {
            tratarErro(e);
        } finally {
            hideModal();
        }
    }

    function configurarFormulario() {
    document.getElementById('form_novo').addEventListener('submit', async event => {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form); // Captura o formulário incluindo arquivos

        if (!validarFormulario(Object.fromEntries(formData))) return;

        try {
            showModal();
            const {data} = await requestApi(`${apiBaseUrl}/?rota=usuarios`, 'POST', formData);

            showAlert('success', data.message, 3000);
            form.reset();
            montarTabela();

        } catch (e) {
            tratarErro(e);
        } finally {
            hideModal();
        }
    });
}


    function validarFormulario(dados) {
        if (dados.usuario_senha.length < 6) {
            showAlert('danger', 'A senha deve ter pelo menos 6 caracteres', 3000);
            return false;
        }

        if (dados.usuario_senha !== dados.usuario_senha2) {
            showAlert('danger', 'Senhas não conferem', 3000);
            return false;
        }

        delete dados.usuario_senha2;
        return true;
    }

    function tratarErro(e) {
        const {
            status,
            message
        } = e.response?.data || {};

        if (status === 'invalid_token' || status === 'token_empty') {
            window.location.href = '?secao=login';
        } else if (status === 'error' || status === 'forbidden' || status === 'invalid_email') {
            showAlert('danger', message, 3000);
        } else if (status === 'duplicated') {
            showAlert('info', message, 3000);
        }
    }
</script>
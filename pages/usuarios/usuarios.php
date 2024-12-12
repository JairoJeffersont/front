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
                    <div id="alerta"></div>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" required>
                        </div>
                        <div class="col-md-4 col-12">
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
                                <option value="1" selected>Ativado</option>
                                <option value="0">Desativado</option>
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
                        <div class="col-md-3 col-12">
                            <div class="file-upload">
                                <input type="file" id="file-input" name="usuario_foto" style="display: none;" />
                                <button id="file-button" type="button" class="btn btn-primary btn-sm"><i class="bi bi-camera-fill"></i> Escolher Foto</button>
                                <button type="button" class="btn btn-success btn-sm" id="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Aniversário</th>
                                    <th scope="col">Telefone</th>
                                    <th scope="col">Nivel</th>
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
    
    $(document).ready(function() {
        montarTabela();
        $("#btn_salvar").click(function() {
            if ($("#usuario_senha").val() !== $("#usuario_senha2").val()) {
                showAlert('danger', 'Senhas não são iguais', 3000);
            } else {
                inserirUsuario();
            }
        });
    });


    async function montarTabela() {
        $("#tabela").empty()
        try {
            const response = await requestApi(`${apiBaseUrl}/?rota=usuarios`, 'GET');
            if (response.status === 'success' && response.dados) {
                $.each(response.dados, function(index, usuario) {
                    $("#tabela").append(
                        `<tr><td style="white-space: nowrap;"><a href="?secao=usuario&id=${usuario.usuario_id}">${usuario.usuario_nome}</a></td>
                         <td style="white-space: nowrap;">${usuario.usuario_email}</td>
                         <td style="white-space: nowrap;">${usuario.usuario_aniversario}</td>
                         <td style="white-space: nowrap;">${usuario.usuario_telefone}</td>
                         <td style="white-space: nowrap;">${usuario.usuario_nivel == 1 ? 'Administrador' : 'Assessor'}</td>
                         <td style="white-space: nowrap;">${usuario.usuario_ativo ? 'Sim' : 'Não'}</td></tr>`
                    );
                });
            } else if (response.status === 'empty') {
                $("#tabela").empty().append(`<tr><td colspan="6">${response.message}</td></tr>`);
            }
        } catch (error) {
            $("#tabela").empty().append(`<tr><td colspan="6">${error.xhr.responseJSON.message}</td></tr>`);
        }
    }

    async function inserirUsuario() {
        try {
            const form = document.getElementById('form_novo');
            const formData = new FormData(form);

            const response = await requestApi(`${apiBaseUrl}/?rota=usuarios`, 'POST', formData, true);

            if (response.status === 'success') {
                montarTabela();
                form.reset();
                showAlert('success', response.message, 3000)
            }
        } catch (error) {
            showAlert('danger', error.xhr.responseJSON.message, 3000)
        }
    }
</script>
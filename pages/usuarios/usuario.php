<div class="d-flex" id="wrapper">
    <?php include 'pages/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include 'pages/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" onclick="window.location.href = '?secao=usuarios'" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-people-fill"></i> Editar usuário</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div id="alerta"></div>
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
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="usuario_ativo" required>
                                <option value="true">Ativado</option>
                                <option value="false" selected>Desativado</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="file-upload">
                                <button type="submit" class="btn btn-primary btn-sm" id="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                                <button type="submit" class="btn btn-danger btn-sm" id="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalLoading" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" >
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

        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');

        buscarUsuario(id);

        const form = document.getElementById('form_novo');

        const btnAtualizar = document.getElementById('btn_atualizar');
        const btnApagar = document.getElementById('btn_apagar');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            if (event.submitter === btnAtualizar) {
                const confirmUpdate = window.confirm('Tem certeza de que deseja atualizar esse usuário?');
                if (confirmUpdate) {
                    showModal();
                    await atualizarUsuario(id, form);
                }
            } else if (event.submitter === btnApagar) {
                const confirmDelete = window.confirm('Tem certeza de que deseja apagar esse usuário?');
                if (confirmDelete) {
                    await apagarUsuario(id);
                }
            }
        });

    };

    async function buscarUsuario(id) {
        showModal();
        try {
            const url = `${apiBaseUrl}/usuarios/${id}?cliente_id=${localStorage.getItem('cliente_id')}`;
            const method = 'get';

            const response = await requestApi(url, method, null, localStorage.getItem('usuario_token'));

            if (response.data.status == 200) {

                const campos = [{
                        nome: 'usuario_nome',
                        campo: document.querySelector('input[name="usuario_nome"]')
                    },
                    {
                        nome: 'usuario_email',
                        campo: document.querySelector('input[name="usuario_email"]')
                    },
                    {
                        nome: 'usuario_telefone',
                        campo: document.querySelector('input[name="usuario_telefone"]')
                    },
                    {
                        nome: 'usuario_aniversario',
                        campo: document.querySelector('input[name="usuario_aniversario"]')
                    },
                    {
                        nome: 'usuario_ativo',
                        campo: document.querySelector('select[name="usuario_ativo"]')
                    }
                ];

                campos.forEach(item => {
                    if (response.data.dados[item.nome]) {
                        if (item.campo.tagName === 'SELECT') {
                            item.campo.value = response.data.dados[item.nome];
                        } else {
                            item.campo.value = response.data.dados[item.nome];
                        }
                    }
                });
            }
            hideModal()
        } catch (e) {
            hideModal()
            if (e.error.status == 404 || e.error.status == 500) {
                showAlert('danger', e.error.message, 0);
            }
        }
    }

    async function atualizarUsuario(id, form) {
        const formData = new FormData(form);

        const dados = {};
        formData.forEach((value, key) => {
            dados[key] = value;
        });

        try {
            const url = `${apiBaseUrl}/usuarios/${id}?cliente_id=${localStorage.getItem('cliente_id')}`;
            const method = 'PUT';

            const response = await requestApi(url, method, dados, localStorage.getItem('usuario_token'));
            hideModal()
            showAlert('success', response.data.message, 3000);

        } catch (e) {
            hideModal()
            showAlert('danger', e.error.message, 3000);
        }
    }

    async function apagarUsuario(id) {
        try {
            const url = `${apiBaseUrl}/usuarios/${id}?cliente_id=${localStorage.getItem('cliente_id')}`;
            const method = 'DELETE';

            const response = await requestApi(url, method, null, localStorage.getItem('usuario_token'));

            hideModal();
            showAlert('success', response.data.message + 'Aguarde...', 3000);
            setTimeout(function() {
                window.location.href = '?secao=usuarios'
            }, 1000)

        } catch (e) {
            hideModal();
            showAlert('danger', e.error.message, 3000);
        }
    }
</script>
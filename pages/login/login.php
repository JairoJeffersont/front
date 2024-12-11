<link href="css/login.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">
        <img src="img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-4">Gabinete Digital</h2>
        <div id="alerta"></div>
        <form id="form_login" class="form-group">
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" value="jairojeffersont@gmail.com" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="senha" id="senha" placeholder="Senha" value="intell01" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" id="btn-logar" class="btn btn-login">Entrar</button>
            </div>
        </form>
        <p class="mt-3 link">Esqueceu a senha? | <a href="?secao=cadastro">Faça seu cadastro</a></p>
        <p class="mt-3 copyright"><?php echo date('Y') ?> | JS Digital System</p>
    </div>
</div>

<div class="modal fade" id="modalLoading" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img class="rounded mx-auto d-block" width="200" src="img/loading.gif">
            </div>
        </div>
    </div>
</div>

<script>
    async function logar() {
        try {
            const url = 'http://localhost:3000/api/login';
            const method = 'POST';

            const data = {
                email: document.getElementById('email').value,
                senha: document.getElementById('senha').value
            };

            const response = await requestApi(url, method, data);

            localStorage.setItem('usuario_id', response.data.id);
            localStorage.setItem('usuario_nome', response.data.nome);
            localStorage.setItem('usuario_token', response.data.token);
            localStorage.setItem('cliente_id', response.data.cliente_id);

            showAlert('success', 'Login feito com sucesso! Aguarde...', 0);

            setTimeout(function() {
                window.location.href = '?secao=usuarios'
            }, 1000)

        } catch (e) {

            if (e.error.status == 404 || e.error.status == 401) {
                showAlert('info', e.error.message, 3000);
            }

            if (e.error.status == 401 || e.error.status == 500) {
                showAlert('danger', e.error.message, 3000);
            }
            
        }
    }

    document.getElementById('btn-logar').addEventListener('click', logar);

</script>
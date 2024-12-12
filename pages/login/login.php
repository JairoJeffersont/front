<link href="css/login.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">
        <img src="img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-4">Gabinete Digital</h2>
        <div id="alerta"></div>
        <form id="form_login" class="form-group">
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" value="admin@admin.com" required>
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
    document.getElementById('btn-logar').addEventListener('click', logar);

    async function logar() {
        try {
            const url = `${apiBaseUrl}/?rota=login`;
            const method = 'POST';

            const data = {
                email: document.getElementById('email').value,
                senha: document.getElementById('senha').value
            };

            // Faz a requisição usando o requestApi
            const response = await requestApi(url, method, data, false); // `false` indica que não é multipart/form-data

            if (response.status === 'success') {
                localStorage.setItem('usuario_token', response.token);
                showAlert('success', 'Login feito com sucesso! Aguarde...', 0);
                setTimeout(function() {
                    window.location.href = '?secao=usuarios';
                }, 1000);
            }
        } catch (e) {
            if (e.xhr && e.xhr.responseJSON) {
                const responseData = e.xhr.responseJSON;

                if (responseData.status === 'not_found' || responseData.status === 'wrong_password') {
                    showAlert('danger', responseData.message, 3000);
                }

                if (responseData.status === 'deactivated') {
                    showAlert('info', responseData.message, 3000);
                }
            } else {
                console.error('Erro inesperado:', e);
                showAlert('danger', 'Ocorreu um erro inesperado. Tente novamente mais tarde.', 3000);
            }
        }

    }
</script>
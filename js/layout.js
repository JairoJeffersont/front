function showModal() {
    $('#modalLoading').modal('show');
}

function hideModal() {
    setTimeout(function() {
        $('#modalLoading').modal('hide');
    }, 500);
}

function showAlert(type, message, time) {
    // Validando os parâmetros
    if (!type || !message) {
        console.error("Tipo e mensagem são obrigatórios.");
        return;
    }
    if (typeof time !== 'number') {
        time = 0; // Se não for um número, o tempo será indefinido
    }

    // Criando o alerta
    const alertHtml = `<div class="alert alert-${type} mb-2 custom-alert px-2 py-1" role="alert">${message}</div>`;
    const $alerta = $("#alerta");

    // Exibindo o alerta com animação
    $alerta.empty().html(alertHtml).fadeIn();

    // Se o tempo for maior que 0, escondemos o alerta após o tempo
    if (time > 0) {
        setTimeout(function() {
            $alerta.fadeOut();
        }, time);
    }
}

$('form').on('keydown', function(event) {
    if (event.key === "Enter") {
        event.preventDefault(); // Impede o envio do formulário
    }
});
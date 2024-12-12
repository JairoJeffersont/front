decodificarJWT()



function showModal() {
    $('#modalLoading').modal('show');
}


function hideModal() {
    setTimeout(function () {
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
        setTimeout(function () {
            $alerta.fadeOut();
        }, time);
    }
}

$('form').on('keydown', function (event) {
    if (event.key === "Enter") {
        event.preventDefault(); // Impede o envio do formulário
    }
});

function montarPagination(resp) {
    $("#pagination").empty();

    if (resp.links) {
        // Extrai a página atual a partir do link "atual"
        const urlAtual = resp.links.atual;
        const urlObj = new URL(urlAtual);
        const paginaAtual = parseInt(urlObj.searchParams.get("pagina"));

        // Extrai a página da última página a partir do link "ultima"
        const urlUltima = resp.links.ultima;
        const urlUltimaObj = new URL(urlUltima);
        const totalPaginas = parseInt(urlUltimaObj.searchParams.get("pagina"));

        // Gerar os itens de paginação
        for (let i = 1; i <= totalPaginas; i++) {
            const isActive = i === paginaAtual ? 'active' : ''; // Verifica se a página é a atual

            $("#pagination").append(`
                <li class="page-item ${isActive}" data-pagina="${i}">
                    <a class="page-link" href="#">${i}</a>
                </li>
            `);
        }
    }
}


function decodificarJWT() {
    const token = localStorage.getItem('usuario_token');

    if (!token) {
        alert('Por favor, insira um token JWT.');
        return;
    }

    try {
        const partes = token.split('.');

        if (partes.length !== 3) {
            throw new Error('Token JWT inválido.');
        }

        const header = JSON.parse(atob(partes[0]));

        const payload = JSON.parse(atob(partes[1]));

        $("#nome_usuario_menu").text(payload.usuario_nome)
        $("#perfil_menu").attr('href', `?secao=usuario&id=${payload.usuario_id}`)


    } catch (error) {
        console.error('Erro ao decodificar o token:', error);
    }
}



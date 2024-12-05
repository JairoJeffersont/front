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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text-fill"></i> Proposições do gabinete</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Proposições de autoria do gabinete</b></p>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <div id="alerta"></div>
                            <form class="row g-2 form_custom mb-0">
                                <div class="col-md-1 col-6">
                                    <select class="form-select form-select-sm" id="ano" required>

                                    </select>

                                </div>
                                <div class="col-md-1 col-6">
                                    <select class="form-select form-select-sm" id="tipo" required>
                                        <option value="PL" selected>PL</option>
                                        <option value="REQ">REQ</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" id="autoria" required>
                                        <option value="true" selected>Autoria única</option>
                                        <option value="false">Autoria compartilhada</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" id="itens" required>
                                        <option value="5">5 itens</option>
                                        <option value="10" selected>10 itens</option>
                                        <option value="25">25 itens</option>
                                        <option value="50">50 itens</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Titulo</th>
                                    <th scope="col">Ementa</th>
                                    <th scope="col">Autoria</th>
                                </tr>
                            </thead>
                            <tbody id="tabela">
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination custom-pagination mb-0" id="navigation">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
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
    $(document).ready(function() {

        let anoAtual = new Date().getFullYear();

        for (let i = 2019; i <= anoAtual; i++) {
            $("#ano").append(`<option value="${i}">${i}</option>`);
        }

        let ano = $("#ano").val();
        let itens = $("#itens").val();
        let pagina = 1;
        let tipo = $("#tipo").val();
        let ordem = 'desc';
        let ordenarPor = 'id';
        let autoria = $("#autoria").val() === "true";

        carregarDados(ano, itens, pagina, tipo, ordem, ordenarPor, autoria, function(resp) {
            montarTabela(resp.dados);
        });

        $('#ano, #tipo, #itens, #autoria').change(function() {
            ano = $("#ano").val();
            itens = $("#itens").val();
            tipo = $("#tipo").val();
            autoria = $("#autoria").val() === "true";

            carregarDados(ano, itens, pagina, tipo, ordem, ordenarPor, autoria, function(resp) {
                montarTabela(resp.dados);
            });

        });
    });


    function montarTabela(dados) {
        $("#tabela").empty()

        if (dados && dados.length > 0) {
            $.each(dados, function(index, proposicao) {
                $("#tabela").append(`<tr><td style="white-space: nowrap;"><a href="?secao=proposicao&id=${proposicao.proposicao_id}">${proposicao.proposicao_titulo}</td><td>${proposicao.proposicao_ementa}</td><td>${proposicao.proposicao_autoria_unica}</a></td></tr>`);
            });
        } else {
            $("#tabela").append(`<tr style="white-space: nowrap;"><td colspan=2>Nenhum registro encontrado</td></tr>`);
        }
    }

    function carregarDados(ano, itens, pagina, tipo, ordem, ordenarPor, autoria, callback) {

        var params = {
            ano: ano,
            itens: itens,
            pagina: pagina,
            tipo: tipo,
            ordem: ordem,
            ordenarPor: ordenarPor
        };

        baixarDadosAPI('proposicoes', params, function(resp) {

            if (resp.status == 200) {
                var resultados = [];

                $.each(resp.dados, function(index, proposicao) {
                    if (autoria === proposicao.proposicao_autoria_unica) {
                        resultados.push(proposicao);
                    }
                });

                callback({
                    dados: resultados
                });
            }

            if (resp.status == 204) {
                showAlert('info', resp.message, 2000);
                callback(resp);
            }
        });

    }
</script>
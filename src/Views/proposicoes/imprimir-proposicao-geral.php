<?php

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;
use GabineteDigital\Controllers\ProposicaoTemaController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();
$temaController = new ProposicaoTemaController();

$proposicaoIdGet = $_GET['id'];

$buscaProposicao = $proposicaoController->buscarProposicaoDB('proposicao_id', $proposicaoIdGet);

if ($buscaProposicao['status'] == 'not_found' || $buscaProposicao['status'] == 'error') {
    header('Location: ?secao=proposicoes');
}

$buscaNota = $notaController->buscarNotaTecnica('nota_proposicao', $proposicaoIdGet);

?>


<style>
    body {
        background-image: none !important;
        background-color: white !important;
    }

    @media print {

        @page {
            margin: 0;
            margin-top: 15mm;
            margin-bottom: 15mm;
            size: A4 portrait;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }



        header,
        footer {
            display: none !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    }
</style>

<script>
    window.onload = function() {
        window.print();
        window.onafterprint = function() {
            window.close();
        };
    };
</script>

<div class="container-fluid p-2">
    <div class="row ">
        <div class="col-12">
            <div class="card" style="background: none; border: none;">
                <div class="card-body text-center" style="background: none;">
                    <img src="public/img/brasaooficialcolorido.png" class="img-fluid mb-2" style="width: 150px;" />
                    <h5 class="card-title mb-2">Gabinete - <?php echo $_SESSION['cliente_deputado_nome'] ?></h5>
                    <p class="card-text" style="font-size: 1.4em;">Ficha da proposição </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3 mb-2 d-flex justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h5 class="card-title mb-4"><?php echo $buscaProposicao['dados'][0]['proposicao_titulo'] ?></h5>
                    <?php

                    if ($buscaNota['status'] == 'success') {
                        echo '<p class="card-text mb-3">' . $buscaNota['dados'][0]['nota_proposicao_apelido'] . '</p>';
                        echo '<p class="card-text mb-3"><b>Resumo</b></p>';
                        echo '<p class="card-text mb-3">' . $buscaNota['dados'][0]['nota_proposicao_resumo'] . '</p>';
                        echo '<p class="card-text mb-3"><b>Ementa</b></p>';
                        $ementa = html_entity_decode($buscaProposicao['dados'][0]['proposicao_ementa']);
                        $ementa = preg_replace('/<\/?p>/', '', $ementa);
                        $ementa = strip_tags($ementa);
                        echo '<p class="card-text mb-0"><em>' . $ementa . '</em></p>';
                    } else if ($buscaNota['status'] == 'not_found') {
                        echo '<p class="card-text mb-3"><b>Ementa</b></p>';
                        $ementa = html_entity_decode($buscaProposicao['dados'][0]['proposicao_ementa']);
                        $ementa = preg_replace('/<\/?p>/', '', $ementa);
                        $ementa = strip_tags($ementa);
                        echo '<p class="card-text mb-0"><em>' . $ementa . '</em></p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row d-flex mb-2 justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Informações gerais</h6>
                    <hr>

                    <p class="card-text mb-1"><i class="bi bi-calendar2-week"></i> Data de apresentação: <?php echo date('d/m/Y', strtotime($buscaProposicao['dados'][0]['proposicao_apresentacao'])) ?></p>
                    <p class="card-text mb-2"><i class="bi bi-archive"></i> Situação: <?php echo ($buscaProposicao['dados'][0]['proposicao_apresentacao']) ? 'Arquivada' : 'Em tramitação' ?></p>
                    <?php

                    if ($buscaProposicao['dados'][0]['proposicao_aprovada']) {
                        echo '<p class="card-text mb-3"><b>Proposição Aprovada</b></p>';
                    }

                    ?>

                    <hr>
                    <p class="card-text mb-0">
                        <i class="bi bi-person-fill"></i> Autor(a): <?php echo $buscaProposicao['dados'][0]['proposicao_autor']; ?>

                    </p>

                </div>
            </div>
        </div>
    </div>
    <div class="row d-flex mb-2 justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Nota técnica</h6>
                    <hr>
                    <?php

                    if ($buscaNota['status'] == 'success' && !empty($buscaNota['dados'])) {
                        echo $buscaNota['dados'][0]['nota_texto'];
                    } else if ($buscaNota['status'] == 'not_found') {
                        echo '<p class="card-text mb-3">Não existe uma nota técnica para essa proposição</p>';
                    }

                    ?>

                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex mb-2 justify-content-center ">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Últimas Tramitações</h6>
                    <hr>
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Despacho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php


                                $itensPorPagina = 5;
                                $paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;


                                $buscaTramitacoes = $proposicaoController->buscarTramitacoesDB('tramitacao_proposicao', $proposicaoIdGet);

                                $totalPaginas = ceil(count($buscaTramitacoes['dados']) / $itensPorPagina);

                                $offset = ($paginaAtual - 1) * $itensPorPagina;

                                if ($buscaTramitacoes['status'] == 'success') {
                                    foreach (array_slice($buscaTramitacoes['dados'], $offset, $itensPorPagina) as $tramitacao) {
                                        echo '<tr>';

                                        echo '<td>' . date('d/m', strtotime($tramitacao['tramitacao_criado_em'])) . '</td>';
                                        echo '<td>' . $tramitacao['proposicao_tramitacao_nome'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaTramitacoes['status'] == 'not_found') {
                                    echo '<tr>';

                                    echo '<td colspan="2">Nenhuma tramitação encontrada</td>';
                                    echo '</tr>';
                                } else {
                                    echo '<tr>';

                                    echo '<td colspan="2">' . $buscaTramitacoes['message'] . '</td>';
                                    echo '</tr>';
                                }


                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>





</div>
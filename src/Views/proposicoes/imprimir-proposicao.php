<?php

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$proposicaoIdGet = $_GET['id'];

$buscaProposicao = $proposicaoController->buscaProposicao('proposicao_id', $proposicaoIdGet);

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
                    <h5 class="card-title mb-2">Gabinete do Deputado <?php echo $_SESSION['cliente_deputado_nome'] ?></h5>
                    <p class="card-text" style="font-size: 1.4em;">Ficha da proposição </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3 mb-2 d-flex justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h5 class="card-title mb-4"><?php echo $buscaProposicao['dados'][0]['proposicao_titulo']; ?></h5>
                    <?php

                    if ($buscaNota['status'] == 'success' && !empty($buscaNota['dados'])) {
                        echo '<p class="card-text mb-3">' . $buscaNota['dados'][0]['nota_proposicao_apelido'] . '</p>';
                        echo '<p class="card-text mb-3"><b>Resumo</b></p>';
                        echo '<p class="card-text mb-3">' . $buscaNota['dados'][0]['nota_proposicao_resumo'] . '</p>';
                        echo '<p class="card-text mb-3"><b>Ementa</b></p>';
                        echo '<p class="card-text mb-0"><em>' . $buscaProposicao['dados'][0]['proposicao_ementa'] . '</em></p>';
                    } else if ($buscaNota['status'] == 'not_found') {
                        echo '<p class="card-text mb-3"><b>Ementa</b></p>';
                        echo '<p class="card-text mb-0"><em>' . $buscaProposicao['dados'][0]['proposicao_ementa'] . '</em></p>';
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
                    <p class="card-text mb-2"><i class="bi bi-archive"></i> Situação: <?php echo $buscaProposicao['dados'][0]['proposicao_arquivada'] ? '<b>Arquivada</b>' : 'Em tramitação' ?></p>
                    <?php echo $buscaProposicao['dados'][0]['proposicao_aprovada'] ? '<p class="card-text mb-3"><b>Proposição Aprovada</b></p>' : '' ?>

                    <?php

                    if (!empty($buscaProposicao['dados'][0]['proposicao_principal'])) {
                        echo '<p class="card-text mb-0"><i class="bi bi-info-circle"></i> Essa proposição foi apensada ao: <b>' . $proposicaoController->buscaProposicao('proposicao_id', $buscaProposicao['dados'][0]['proposicao_principal'])['dados'][0]['proposicao_titulo'] . '</b></p>';
                    } else {
                        echo '<p class="card-text mb-0"><i class="bi bi-info-circle"></i> Essa proposição não foi apensada ou é a proposição principal</p>';
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
                        <table class="table table-hover table-bordered table-striped mb-0 ">
                            <thead>
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Despacho</th>
                                    <th scope="col">Órgão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaTramitacoes = $proposicaoController->buscarTramitacoes($proposicaoIdGet);

                                if ($buscaTramitacoes['status'] == 'success' && is_array($buscaTramitacoes['dados'])) {
                                    $itens = 10;
                                    $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

                                    usort($buscaTramitacoes['dados'], function ($a, $b) {
                                        return strtotime($b['dataHora']) - strtotime($a['dataHora']);
                                    });

                                    $totalRegistros = count($buscaTramitacoes['dados']);
                                    $totalPagina = ceil($totalRegistros / $itens);

                                    $offset = ($pagina - 1) * $itens;

                                    foreach (array_slice($buscaTramitacoes['dados'], $offset, $itens) as $tramitacao) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y H:i', strtotime($tramitacao['dataHora'])) . '</td>';
                                        echo '<td>' . htmlspecialchars($tramitacao['despacho']);
                                        echo '</td>';
                                        echo '<td>' . htmlspecialchars($tramitacao['siglaOrgao']) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaTramitacoes['status'] == 'error') {
                                    echo '<p class="card-text">' . $buscaTramitacoes['message'] . '</p>';
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
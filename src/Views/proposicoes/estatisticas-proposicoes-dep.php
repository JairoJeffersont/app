<?php

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

$tiposPermitidos = ['PL', 'PEC', 'REQ', 'PLP', 'PRL'];

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background">
                    <i class="bi bi-file-earmark-text"></i> Estatísticas das Proposições | <?php echo $_SESSION['cliente_deputado_tipo'] ?>
                </div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">
                        Nesta seção, você encontrará estatísticas sobre a produção legislativa. Serão exibidas apenas as proposições originadas no gabinete, ou seja, aquelas redigidas pela equipe.
                    </p>
                    <p class="card-text mb-0">
                        As informações são disponibilizadas pela Câmara dos Deputados.
                    </p>
                </div>
            </div>

            <div class="row">


                <div class="col-12">
                    <div class="card mb-2 ms-0">
                        <div class="card-header bg-success text-white px-2 py-1 card-description"> Proposições apresentadas | Arquivamento</div>
                        <div class="card-body p-1">
                            <div class="table-responsive">

                                <table class="table table-hover table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Tipo</th>

                                            <th scope="col">Tramitando</th>
                                            <th scope="col">Aprovadas</th>
                                            <th scope="col">Arquivadas</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $buscaProposicoeArquivada = $proposicaoController->buscarProposicoesGabineteArquivada($_SESSION['cliente_deputado_nome']);

                                        foreach ($buscaProposicoeArquivada['dados'] as $proposicaoArquivada) {
                                            if (in_array($proposicaoArquivada['proposicao_tipo'], $tiposPermitidos)) {
                                                echo '<tr>';
                                                echo '<td>' . $proposicaoArquivada['proposicao_tipo'] . '</td>';
                                                echo '<td>' . $proposicaoArquivada['total_nao_arquivada'] . '</td>';
                                                echo '<td>' . $proposicaoArquivada['total_aprovada'] . '</td>';
                                                echo '<td>' . $proposicaoArquivada['total_arquivada'] . '</td>';
                                                echo '<td>' . $proposicaoArquivada['total_nao_arquivada'] +  $proposicaoArquivada['total_arquivada'] . '</td>';
                                                echo '</tr>';
                                            }
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card mb-2 ">
                        <div class="card-header bg-primary text-white px-2 py-1 card-description"> Proposições apresentadas | Anos</div>
                        <div class="card-body p-1">
                            <div class="accordion" id="accordionPanelsStayOpenExample">


                                <?php
                                $buscaProposicoeAno = $proposicaoController->buscarProposicoesGabineteAno($_SESSION['cliente_deputado_nome']);


                                $anos = array_column($buscaProposicoeAno['dados'], 'proposicao_ano');
                                $anosUnicos = array_unique($anos);


                                foreach ($anosUnicos as $ano) {
                                    echo '<div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed p-2" style="font-size: 0.5em;" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $ano . '" aria-expanded="false" aria-controls="panelsStayOpen-collapse' . $ano . '">
                                                ' . $ano . '
                                                </button>
                                            </h2>
                                            <div id="panelsStayOpen-collapse' . $ano . '" class="accordion-collapse collapse">
                                                <div class="accordion-body">';
                                    foreach ($buscaProposicoeAno['dados'] as $proposicoesAno) {
                                        if (in_array($proposicoesAno['proposicao_tipo'], $tiposPermitidos) && $proposicoesAno['proposicao_ano'] == $ano) {
                                            echo '<p class="card-text mb-1"><i class="bi bi-dot"></i> ' . $proposicoesAno['proposicao_tipo'] . ' - ' . $proposicoesAno['total'] . ' | <a href="?secao=proposicoes&ano='.$ano.'&tipo='.strtolower($proposicoesAno['proposicao_tipo']).'">Ver</a></p>';
                                        }
                                    }
                                    echo '</div>
                                            </div>
                                        </div>';
                                }

                                ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-newspaper"></i> Proposição</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Nesta seção, você pode consultar informações de uma proposição.</p>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <div class="card mb-2">
                        <div class="card-body card-description">
                            <h5 class="card-title mb-3"><?php echo $buscaProposicao['dados'][0]['proposicao_titulo'] ?></h5>
                            <p class="card-text"><?php echo $buscaProposicao['dados'][0]['proposicao_ementa'] ?></p>
                            <p class="card-text mb-1"><i class="bi bi-calendar2-week"></i> Data de apresentação: <?php echo date('d/m', strtotime($buscaProposicao['dados'][0]['proposicao_apresentacao'])) ?></p>
                            <p class="card-text mb-3"><i class="bi bi-archive"></i> Arquivada: <?php echo $buscaProposicao['dados'][0]['proposicao_arquivada'] ? '<b>Arquivada</b>' : 'Em tramitação' ?></p>
                            <?php echo $buscaProposicao['dados'][0]['proposicao_aprovada'] ? '<p class="card-text mb-3"><b>Proposição Aprovada</b></p>' : '' ?>

                            <?php

                            if (!empty($buscaProposicao['dados'][0]['proposicao_principal'])) {
                                echo '<p class="card-text mb-0">Essa proposição foi apensada ao: <b><a href="?secao=proposicao&id=' . $buscaProposicao['dados'][0]['proposicao_principal'] . '">' . $proposicaoController->buscaProposicao('proposicao_id', $buscaProposicao['dados'][0]['proposicao_principal'])['dados'][0]['proposicao_titulo'] . '</a></b></p>';
                            } else {
                                echo '<p class="card-text mb-0">Essa proposição não foi apensada</p>';
                            }

                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card mb-2">
                        <div class="card-body card-description">
                            <div class="accordion" id="accordionPanelsStayOpenExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" style="font-size: 0.5em" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                            <i class="bi bi-file-text"></i> &nbsp; &nbsp;Ver inteiro teor
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <?php

                                            $buscaDet = $proposicaoController->buscarDetalhe($buscaProposicao['dados'][0]['proposicao_id']);

                                            if ($buscaDet['status'] == 'success' && !empty($buscaDet['dados']['urlInteiroTeor'])) {
                                                $url_pdf = $buscaDet['dados']['urlInteiroTeor'];
                                                echo "<embed src='$url_pdf' type='application/pdf' width='100%' height='1000px'>";
                                            } else {
                                                echo '<p class="card-text">Documento não disponível</p>';
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
            <div class="card mb-2 card-description">
            <div class="card-header bg-primary text-white px-2 py-1"><i class="bi bi-fast-forward-btn"></i> Tramitações</div>

                <div class="card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Despacho</th>
                                    <th scope="col">Órgão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaTramitacoes = $proposicaoController->buscarTramitacoes($buscaProposicao['dados'][0]['proposicao_id']);

                                if ($buscaTramitacoes['status'] == 'success' && is_array($buscaTramitacoes['dados'])) {
                                    $itens = isset($_GET['itens']) ? $_GET['itens'] : 10;
                                    $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

                                    // Ordena as tramitações por dataHora em ordem decrescente
                                    usort($buscaTramitacoes['dados'], function ($a, $b) {
                                        return strtotime($b['dataHora']) - strtotime($a['dataHora']);
                                    });

                                    $totalRegistros = count($buscaTramitacoes['dados']);
                                    $totalPagina = ceil($totalRegistros / $itens);

                                    $offset = ($pagina - 1) * $itens;

                                    foreach (array_slice($buscaTramitacoes['dados'], $offset, $itens) as $tramitacao) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($tramitacao['dataHora'])) . '</td>';
                                        echo '<td>' . htmlspecialchars($tramitacao['despacho']) . '</td>';
                                        echo '<td>' . htmlspecialchars($tramitacao['siglaOrgao']) . '</td>';
                                        echo '</tr>';
                                    }
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                    <?php
                    if ($totalPagina > 0 && $totalPagina != 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                        echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=proposicao&id=' . $proposicaoIdGet . '&itens=' . $itens . '&pagina=1">Primeira</a></li>';

                        for ($i = 1; $i < $totalPagina - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicao&id=' . $proposicaoIdGet . '&itens=' . $itens . '&pagina=' . $pageNumber . '">' . $pageNumber . '</a></li>';
                        }

                        echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=proposicao&id=' . $proposicaoIdGet . '&itens=' . $itens . '&pagina=' . $totalPagina . '">Última</a></li>';
                        echo '</ul>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>
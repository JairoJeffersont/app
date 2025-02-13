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
                                            // URL do arquivo PDF
                                            $url_pdf = 'https://www.camara.leg.br/proposicoesWeb/prop_mostrarintegra?codteor=2853918';

                                            // Exibindo o PDF com o embed
                                            echo "<embed src='$url_pdf' type='application/pdf' width='100%' height='1000px'>";
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





            </div>
        </div>
    </div>
</div>
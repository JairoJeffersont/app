<?php


ob_start();

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$anoGet = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$autorGet = $_SESSION['cliente_deputado_nome'];
$tipoget = isset($_GET['tipo']) ? $_GET['tipo'] : 'pl';
$ordenarPorGet = isset($_GET['ordenarPor']) ? $_GET['ordenarPor'] : 'proposicao_numero';
$ordemGet = isset($_GET['ordem']) ? $_GET['ordem'] : 'desc';
$itensGet = isset($_GET['itens']) ? (int)$_GET['itens'] : 10;
$paginaGet = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$arquivadoGet = isset($_GET['arquivado']) ? (int)$_GET['arquivado'] : 0;

?>


<div class="card mb-2 card-description">
    <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-newspaper"></i> Proposições | <?php echo $_SESSION['cliente_deputado_tipo'] ?></div>
    <div class="card-body p-2">
        <p class="card-text mb-2">Nesta seção, você pode pesquisar pelas proposições do deputado, facilitando o acesso às informações relevantes de forma rápida e organizada.</p>
        <p class="card-text mb-0">As informações são fornecidas pela Câmara dos Deputados.</p>
    </div>
</div>

<div class="col-12">
    <div class="card shadow-sm mb-2">
        <div class="card-body p-2">
            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                <div class="col-md-1 col-2">
                    <input type="hidden" name="secao" value="proposicoes" />
                    <input type="text" class="form-control form-control-sm" name="ano" data-mask="0000" value="<?php echo $anoGet ?>">
                </div>
                <div class="col-md-1 col-10">
                    <select class="form-select form-select-sm" name="tipo" required>
                        <option value="pl" <?php echo $tipoget == 'pl' ? 'selected' : ''; ?>>Projeto de lei</option>
                        <option value="req" <?php echo $tipoget == 'req' ? 'selected' : ''; ?>>Requerimento</option>
                        <option value="pec" <?php echo $tipoget == 'pec' ? 'selected' : ''; ?>>PEC</option>
                        <option value="prl" <?php echo $tipoget == 'prl' ? 'selected' : ''; ?>>Parecer</option>

                    </select>
                </div>
                <div class="col-md-2 col-12">
                    <select class="form-select form-select-sm" name="arquivado" required>
                        <option value="1" <?php echo $arquivadoGet === 1 ? 'selected' : ''; ?>>Arquivado</option>
                        <option value="0" <?php echo $arquivadoGet === 0 ? 'selected' : ''; ?>>Tramitando</option>
                    </select>
                </div>
                <div class="col-md-1 col-4">
                    <select class="form-select form-select-sm" name="itens" required>
                        <option value="5" <?php echo $itensGet == 5 ? 'selected' : ''; ?>>5 itens</option>
                        <option value="10" <?php echo $itensGet == 10 ? 'selected' : ''; ?>>10 itens</option>
                        <option value="25" <?php echo $itensGet == 25 ? 'selected' : ''; ?>>25 itens</option>
                        <option value="50" <?php echo $itensGet == 50 ? 'selected' : ''; ?>>50 itens</option>
                    </select>
                </div>
                <div class="col-md-2 col-6">
                    <select class="form-select form-select-sm" name="ordem" required>
                        <option value="asc" <?php echo $ordemGet == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                        <option value="desc" <?php echo $ordemGet == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                    </select>
                </div>
                <div class="col-md-1 col-2">
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--<div class="card mb-2 ">
    <div class="card-body p-1">
        <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=imprimir-proposicoes" role="button"><i class="bi bi-printer-fill"></i> Imprimir</a>
    </div>
</div>-->
<div class="card shadow-sm mb-2">
    <div class="card-body p-2">
        <div class="table-responsive mb-0">
            <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Ementa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $buscaProposicao = $proposicaoController->buscarProposicoesGabinete($autorGet, $anoGet, $tipoget, $itensGet, $paginaGet, $ordemGet, $ordenarPorGet, $arquivadoGet);

                    if ($buscaProposicao['status'] == 'success') {

                        foreach ($buscaProposicao['dados'] as $proposicao) {

                            $buscaNota = $notaController->buscarNotaTecnica('nota_proposicao', $proposicao['proposicao_id']);

                            if ($buscaNota['status'] == 'success') {
                                $ementa = '<b><em>' . $buscaNota['dados'][0]['nota_proposicao_apelido'] . '</b></em><br>' . $buscaNota['dados'][0]['nota_proposicao_resumo'];
                            } else {
                                $ementa = $proposicao['proposicao_ementa'];
                            }

                            echo '<tr>';
                            echo '<td style="white-space: nowrap;"><a href="?secao=proposicao&id=' . $proposicao['proposicao_id'] . '">' . $proposicao['proposicao_titulo'] . '</a></td>';
                            echo '<td>' . $ementa . '</td>';
                            echo '</tr>';
                        }
                    } else if ($buscaProposicao['status'] == 'empty') {
                        echo '<tr><td colspan="11">' . $buscaProposicao['message'] . '</td></tr>';
                    } else if ($buscaProposicao['status'] == 'error') {
                        echo '<tr><td colspan="11">' . $buscaProposicao['message'] . ' | Código do erro: ' . $buscaProposicao['error_id'] . '</td></tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>
        <?php

        if (isset($buscaProposicao['total_paginas'])) {
            $totalPagina = $buscaProposicao['total_paginas'];
        } else {
            $totalPagina = 0;
        }

        if ($totalPagina > 0 && $totalPagina != 1) {
            echo '<ul class="pagination custom-pagination mt-2 mb-0">';
            echo '<li class="page-item ' . ($paginaGet == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=1&ordenarPor=' . $ordenarPorGet . '&ordem=' . $ordemGet . '&ordenarPor=' . $ordenarPorGet . '&tipo=' . $tipoget . '&arquivado=' . $arquivadoGet . '&ano=' . $anoGet . '">Primeira</a></li>';

            for ($i = 1; $i < $totalPagina - 1; $i++) {
                $pageNumber = $i + 1;
                echo '<li class="page-item ' . ($paginaGet == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPorGet . '&ordem=' . $ordemGet . '&ordenarPor=' . $ordenarPorGet . '&tipo=' . $tipoget . '&arquivado=' . $arquivadoGet . '&ano=' . $anoGet . '">' . $pageNumber . '</a></li>';
            }

            echo '<li class="page-item ' . ($paginaGet == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPorGet . '&ordem=' . $ordemGet . '&ordenarPor=' . $ordenarPorGet . '&tipo=' . $tipoget . '&arquivado=' . $arquivadoGet . '&ano=' . $anoGet . '">Última</a></li>';
            echo '</ul>';
        }
        ?>
    </div>
</div>
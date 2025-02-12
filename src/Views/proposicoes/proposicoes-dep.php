<?php


ob_start();



use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

$anoGet = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$itens = isset($_GET['itens']) ?  $_GET['itens'] : 10;
$pagina = isset($_GET['pagina']) ?  $_GET['pagina'] : 1;
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'desc';
$tipo = isset($_GET['tipo']) ? strtolower(htmlspecialchars($_GET['tipo'])) : 'pl';
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['id', 'numero', 'ano',]) ? htmlspecialchars($_GET['ordenarPor']) : 'id';

$autorGet = $_SESSION['cliente_deputado_nome'];

$autorGet = strtr($autorGet, [
    'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
    'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
    'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
    'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
    'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
    'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
    'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
    'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
    'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
    'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
    'Ç' => 'C', 'ç' => 'c',
    'Ñ' => 'N', 'ñ' => 'n'
]);

$autorGet = strtolower($autorGet);
$autorGet = str_replace(' ', '+', $autorGet);
$autorGet = preg_replace('/[^a-z0-9\+]/', '', $autorGet);




?>

<div class="card mb-2 card-description">
    <div class="card-header bg-primary textcho -white px-2 py-1 card-background"><i class="bi bi-file-earmark-richtext"></i> Proposições | <?php echo $_SESSION['cliente_deputado_tipo'] ?></div>
    <div class="card-body p-2">
        <p class="card-text mb-2">Nesta seção, é possível listar as proposições do gabinete.</p>
        <p class="card-text mb-0">Esses dados são fornecidos pelo serviço de dados abertos da Câmara dos Deputados</p>

    </div>
</div>

<div class="card shadow-sm mb-2 no-print">
    <div class="card-body p-2">
        <form class="row g-2 form_custom mb-0" id="form-busca" method="GET" enctype="application/x-www-form-urlencoded">
            <div class="col-md-1 col-12">
                <input type="hidden" name="secao" value="proposicoes" />
                <input type="text" class="form-control form-control-sm" name="ano" data-mask="0000" value="<?php echo $anoGet ?>">
            </div>

            <div class="col-md-2 col-6">
                <select class="form-select form-select-sm" name="ordem" required>
                    <option value="asc" <?php echo $ordem == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                    <option value="desc" <?php echo $ordem == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select class="form-select form-select-sm" name="itens" required>
                    <option value="5" <?php echo $itens == 5 ? 'selected' : ''; ?>>5 itens</option>
                    <option value="10" <?php echo $itens == 10 ? 'selected' : ''; ?>>10 itens</option>
                    <option value="25" <?php echo $itens == 25 ? 'selected' : ''; ?>>25 itens</option>
                    <option value="50" <?php echo $itens == 50 ? 'selected' : ''; ?>>50 itens</option>
                </select>
            </div>

            <div class="col-md-2 col-6">
                <select class="form-select form-select-sm" name="tipo" required>
                    <option value="PL" <?php echo $tipo == 'pl' ? 'selected' : ''; ?>>PL</option>
                    <option value="PEC" <?php echo $tipo == 'pec' ? 'selected' : ''; ?>>PEC</option>

                </select>
            </div>
            <div class="col-md-1 col-2">
                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>


<div class="card shadow-sm mb-2">
    <div class="card-body p-2">
        <div class="table-responsive mb-0">
            <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                <thead>
                    <tr>
                        <!--<th scope="col">.</th>-->
                        <th scope="col">Título</th>

                        <th scope="col">Ementa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $buscaProposicoes = $proposicaoController->listarProposicoesDeputadoCD($anoGet, $autorGet, $tipo, $itens, $pagina, $ordem, $ordenarPor);

                    if (!empty($buscaProposicoes['dados'])) {
                        foreach ($buscaProposicoes['dados'] as $proposicao) {
                            echo '<tr>';
                            //echo '<td class="text-center">' . ((count($proposicao['proposicao_autores']) > 1) ? '<i class="bi bi-people-fill"></i>' : '<i class="bi bi-person-fill"></i>') . '</td>';
                            echo '<td style="white-space: nowrap;"><a href="?secao=proposicao&id=' . $proposicao['proposicao_id'] . '">' . $proposicao['proposicao_titulo'] . '</a></td>';

                            echo '<td>' . $proposicao['proposicao_ementa'] . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3">Nenhuma proposição encontrada</td></tr>';
                    }

                    ?>
                </tbody>
            </table>
        </div>
        <?php

        if (isset($buscaProposicoes['total_paginas'])) {
            $totalPagina = $buscaProposicoes['total_paginas'];
        } else {
            $totalPagina = 0;
        }

        if ($totalPagina > 0 && $totalPagina != 1) {
            echo '<ul class="pagination custom-pagination mt-2 mb-0">';
            echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link"  href="?secao=proposicoes&itens=' . $itens . '&pagina=1&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&ano=' . $anoGet . '&tipo=' . $tipo . '">Primeira</a></li>';

            for ($i = 1; $i < $totalPagina - 1; $i++) {
                $pageNumber = $i + 1;
                echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itens . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&ano=' . $anoGet . '&tipo=' . $tipo . '">' . $pageNumber . '</a></li>';
            }

            echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link"  href="?secao=proposicoes&itens=' . $itens . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&ano=' . $anoGet . '&tipo=' . $tipo . '">Última</a></li>';
            echo '</ul>';
        }
        ?>
    </div>
</div>
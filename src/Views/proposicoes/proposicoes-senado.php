<?php


ob_start();

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$anoGet = isset($_GET['ano']) ? $_GET['ano'] : date('Y');


function formatarTexto($texto) {
    $texto = strtr($texto, [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'ñ' => 'n',
        'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'Ç' => 'C', 'Ñ' => 'N'
    ]);

    $texto = preg_replace('/\s+/', '+', $texto);

    return $texto;
}

$autorGet = formatarTexto($_SESSION['cliente_deputado_nome']);

$tipoget = isset($_GET['tipo']) ? $_GET['tipo'] : 'pl';
$ordenarPorGet = isset($_GET['ordenarPor']) ? $_GET['ordenarPor'] : 'proposicao_numero';
$ordemGet = isset($_GET['ordem']) ? $_GET['ordem'] : 'desc';
$itensGet = isset($_GET['itens']) ? (int)$_GET['itens'] : 10;
$paginaGet = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

?>

<div class="card mb-2 card-description">
    <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-newspaper"></i> Proposições | <?php echo $_SESSION['cliente_deputado_tipo'] ?></div>
    <div class="card-body p-2">
        <p class="card-text mb-2">Nesta seção, você pode pesquisar pelas proposições do deputado, facilitando o acesso às informações relevantes de forma rápida e organizada.</p>
        <p class="card-text mb-0">As informações são fornecidas pelo Senado Federal.</p>
    </div>
</div>

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

                    <option value="plp" <?php echo $tipoget == 'plp' ? 'selected' : ''; ?>>Projeto de lei complementar</option>
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
                        <th scope="col">Título</th>
                        <th scope="col">Ementa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $autorGet = str_replace(" ", "%20", $autorGet);
                    $buscaProposicoes = $proposicaoController->buscarProposicoesSenado($autorGet, $anoGet, $tipoget);

                    if ($buscaProposicoes['status'] == 'success' && isset($buscaProposicoes['dados']['PesquisaBasicaMateria']['Materias']['Materia'])) {

                        usort($buscaProposicoes['dados']['PesquisaBasicaMateria']['Materias']['Materia'], function ($a, $b) {
                            return $b['Numero'] - $a['Numero']; // Ordem decrescente
                        });

                        $totalRegistros = count($buscaProposicoes['dados']['PesquisaBasicaMateria']['Materias']['Materia']);

                        $totalPaginas = ceil($totalRegistros / $itensGet);

                        $offset = ($paginaGet - 1) * $itensGet;

                        foreach (array_slice($buscaProposicoes['dados']['PesquisaBasicaMateria']['Materias']['Materia'], $offset, $itensGet) as $materia) {

                            $buscaNota = $notaController->buscarNotaTecnica('nota_proposicao',  $materia['Codigo']);


                            if ($buscaNota['status'] == 'success') {
                                $ementa = '<b><em>' . $buscaNota['dados'][0]['nota_proposicao_apelido'] . '</b></em><br>' . $buscaNota['dados'][0]['nota_proposicao_resumo'];
                            } else {
                                $ementa = $materia['Ementa'];
                            }


                            echo '<tr>';
                            echo '<td style="white-space: nowrap;"><a href="?secao=proposicao-senado&id=' . $materia['Codigo'] . '">' . $materia['Sigla'] . ' ' . ltrim($materia['Numero'], '0') . '/' . $materia['Ano'] . '</a></td>';
                            echo '<td>' . $ementa . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="2">Nenhuma proposição encontrada.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php

        if (isset($totalPaginas)) {
            $totalPaginas = $totalPaginas;
        } else {
            $totalPaginas = 0;
        }

        if ($totalPaginas > 0 && $totalPaginas != 1) {
            echo '<ul class="pagination custom-pagination mt-2 mb-0">';
            echo '<li class="page-item ' . ($paginaGet == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=1&tipo=' . $tipoget . '&ano=' . $anoGet . '">Primeira</a></li>';

            for ($i = 1; $i < $totalPaginas - 1; $i++) {
                $pageNumber = $i + 1;
                echo '<li class="page-item ' . ($paginaGet == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=' . $pageNumber . '&tipo=' . $tipoget . '&ano=' . $anoGet . '">' . $pageNumber . '</a></li>';
            }

            echo '<li class="page-item ' . ($paginaGet == $totalPaginas ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=' . $totalPaginas . '&tipo=' . $tipoget . '&ano=' . $anoGet . '">Última</a></li>';
            echo '</ul>';
        }
        ?>
    </div>
</div>
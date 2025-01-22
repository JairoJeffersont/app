<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\ClippingController;

$clippingController = new ClippingController();

$ano = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');

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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-graph-up"></i> Estatísticas</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Nesta seção, você pode consultar estatísticas sobre o conteúdo divulgado do deputado.</p>
                </div>
            </div>

            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-1 col-3">
                                    <input type="hidden" name="secao" value="estatisticas-clipping" />
                                    <input type="text" class="form-control form-control-sm" name="ano" data-mask="0000" value="<?php echo $ano ?>">
                                </div>                               
                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1">Ano</div>
                <div class="card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Ano</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaAno = $clippingController->buscarAno($_SESSION['usuario_cliente']);
                                if ($buscaAno['status'] == 'success') {
                                    $totalAnos = $buscaAno['dados'][0]['total'];
                                    foreach ($buscaAno['dados'] as $clipping) {
                                        $porcentagem = ($clipping['contagem'] / $totalAnos) * 100;
                                        echo '<tr>';
                                        echo '<td>' . date('Y', strtotime($clipping['clipping_data'])) . '</td>';
                                        echo '<td>' . $clipping['contagem'] . '</td>';
                                        echo '<td>' . number_format($porcentagem, 2, ',', '.') . '%</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaAno['status'] == 'not_found') {
                                    echo '<tr>';
                                    echo '<td colspan="3">' . $buscaAno['message'] . '</td>';
                                    echo '</tr>';
                                } else if ($buscaAno['status'] == 'error') {
                                    echo '<tr>';
                                    echo '<td colspan="3">' . $buscaAno['message'] . ' | Código do erro: ' . $buscaAno['error_id'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-2 card-description">
                <div class="card-header bg-secondary text-white px-2 py-1">Tipo</div>
                <div class="card-body p-2">

                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaTipo = $clippingController->buscarTipo($ano, $_SESSION['usuario_cliente']);
                                if ($buscaTipo['status'] == 'success') {
                                    $totalTipos = $buscaTipo['dados'][0]['total'];
                                    foreach ($buscaTipo['dados'] as $tipos) {
                                        $porcentagem = ($tipos['contagem'] / $totalTipos) * 100;
                                        echo '<tr>';
                                        echo '<td>' . $tipos['clipping_tipo_nome'] . '</td>';
                                        echo '<td>' . $tipos['contagem'] . '</td>';
                                        echo '<td>' . number_format($porcentagem, 2, ',', '.') . '%</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaTipo['status'] == 'not_found') {
                                    echo '<tr>';
                                    echo '<td colspan="3">' . $buscaTipo['message'] . '</td>';
                                    echo '</tr>';
                                } else if ($buscaTipo['status'] == 'error') {
                                    echo '<tr>';
                                    echo '<td colspan="3">' . $buscaTipo['message'] . ' | Código do erro: ' . $buscaTipo['error_id'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-2 card-description">
                <div class="card-header bg-success text-white px-2 py-1">Veículo</div>
                <div class="card-body p-2">

                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaOrgao = $clippingController->buscarOrgao($ano, $_SESSION['usuario_cliente']);
                                if ($buscaOrgao['status'] == 'success') {
                                    $totalOrgaos = $buscaOrgao['dados'][0]['total'];
                                    foreach ($buscaOrgao['dados'] as $orgao) {
                                        $porcentagem = ($orgao['contagem'] / $totalOrgaos) * 100;
                                        echo '<tr>';
                                        echo '<td>' . $orgao['orgao_nome'] . '</td>';
                                        echo '<td>' . $orgao['contagem'] . '</td>';
                                        echo '<td>' . number_format($porcentagem, 2, ',', '.') . '%</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaOrgao['status'] == 'not_found') {
                                    echo '<tr>';
                                    echo '<td colspan="3">' . $buscaOrgao['message'] . '</td>';
                                    echo '</tr>';
                                } else if ($buscaOrgao['status'] == 'error') {
                                    echo '<tr>';
                                    echo '<td colspan="3">' . $buscaOrgao['message'] . ' | Código do erro: ' . $buscaOrgao['error_id'] . '</td>';
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
</div>
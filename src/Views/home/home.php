<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\PessoaController;

$pessoaController = new PessoaController();

$buscaAnivesariantes = $pessoaController->buscarPessoa('pessoa_aniversario', date('d/m'));

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="card mb-2 card-description">
                        <div class="card-header bg-primary text-white px-2 py-1 ">
                            <i class="bi bi-cake2"></i> Aniversáriantes do dia - <?php echo date('d/M'); ?>
                        </div>
                        <div class="card-body p-2" style="max-height: 250px;">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-striped mb-0 custom-table" style="background-image: url('./public/img/cake.png'); background-size: cover;">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $itensPorPagina = 5;
                                        $paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1; // Página atual (padrão: 1)
                                        $offset = ($paginaAtual - 1) * $itensPorPagina;

                                        if ($buscaAnivesariantes['status'] == 'success') {
                                            $dados = $buscaAnivesariantes['dados'];
                                            $totalItens = count($dados);
                                            $dadosPaginados = array_slice($dados, $offset, $itensPorPagina); // Dados para a página atual
                                            if (!empty($dadosPaginados)) {
                                                foreach ($dadosPaginados as $pessoa) {
                                                    echo '<tr>';
                                                    echo '<td style="white-space: nowrap;"><a href="?secao=pessoa&id=' . $pessoa['pessoa_id'] . '">' . $pessoa['pessoa_nome'] . '</a></td>';
                                                    echo '<td style="white-space: nowrap;">' . $pessoa['pessoa_tipo_nome'] . '</td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="3">Nenhum aniversariante para hoje</td></tr>';
                                            }
                                        } else if ($buscaAnivesariantes['status'] == 'not_found') {
                                            echo '<tr><td colspan="3">Nenhum aniversariante para hoje</td></tr>';
                                        } else if ($buscaAnivesariantes['status'] == 'error') {
                                            echo '<tr><td colspan="3">' . $buscaAnivesariantes['message'] . ' | Código do erro: ' . $buscaAnivesariantes['error_id'] . '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <?php
                                // Paginação
                                if ($buscaAnivesariantes['status'] == 'success' && !empty($dados)) {
                                    $totalPaginas = ceil($totalItens / $itensPorPagina);
                                    if ($totalPaginas > 1) { // Verifica se há mais de uma página
                                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';

                                        for ($i = 1; $i <= $totalPaginas; $i++) {
                                            $active = $paginaAtual == $i ? 'active' : '';
                                            echo '<li class="page-item ' . $active . '"><a class="page-link" href="?secao=home&pagina=' . $i . '">' . $i . '</a></li>';
                                        }

                                        echo '</ul>';
                                    }
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
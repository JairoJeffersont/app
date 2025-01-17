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
                        <div class="card-header bg-primary card-background text-white px-2 py-1 ">
                            <i class="bi bi-cake2"></i> Aniversáriantes do dia - <?php echo date('d/M'); ?>
                        </div>
                        <div class="card-body p-2">
                            <div class="list-group" style="background-image: url('./public/img/cake.png'); background-size: cover;">
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
                                            $foto = !empty($pessoa['pessoa_foto']) ? $pessoa['pessoa_foto'] : 'public/img/not_found.jpg';

                                            echo '<a href="?secao=pessoa&id=' . $pessoa['pessoa_id'] . '" class="list-group-item list-group-item-action d-flex align-items-center">';
                                            echo '<img src="' . $foto . '" alt="Foto de ' . $pessoa['pessoa_nome'] . '" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">';
                                            echo '<div>';
                                            echo '<strong>' . $pessoa['pessoa_nome'] . '</strong><br>';
                                            echo '<small>' . $pessoa['pessoa_tipo_nome'] . '</small>';
                                            echo '</div>';
                                            echo '</a>';
                                        }
                                    } else {
                                        echo '<div class="list-group-item">Nenhum aniversariante para hoje</div>';
                                    }
                                } else if ($buscaAnivesariantes['status'] == 'not_found') {
                                    echo '<div class="list-group-item">Nenhum aniversariante para hoje</div>';
                                } else if ($buscaAnivesariantes['status'] == 'error') {
                                    echo '<div class="list-group-item">' . $buscaAnivesariantes['message'] . ' | Código do erro: ' . $buscaAnivesariantes['error_id'] . '</div>';
                                }
                                ?>
                            </div>

                            <?php
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
                <div class="col-lg-4 col-12">
                    <div class="card mb-2 card-description">
                        <div class="card-header bg-primary card-background text-white px-2 py-1 ">
                            <i class="bi bi-cake2"></i> Aniversáriantes do dia - <?php echo date('d/M'); ?>
                        </div>
                        <div class="card-body p-2">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';


use GabineteDigital\Controllers\PessoaController;


$pessoaController = new PessoaController();

$municipio = $_GET['municipio'];


?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=estatisticas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-success text-white px-2 py-1">Bairros do município de <?php echo $municipio ?></div>
                <div class="card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Muncípio</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaGenero = $pessoaController->buscarBairro($municipio, $_SESSION['usuario_cliente']);
                                if ($buscaGenero['status'] == 'success') {
                                    $totalPessoas = $buscaGenero['dados'][0]['total'];
                                    foreach ($buscaGenero['dados'] as $genero) {
                                        $porcentagem = ($genero['contagem'] / $totalPessoas) * 100; // Calcula a porcentagem
                                        echo '<tr>';
                                        echo '<td>'.$genero['pessoa_bairro'].'</td>';
                                        echo '<td>' . $genero['contagem'] . '</td>';
                                        echo '<td>' . number_format($porcentagem, 2, ',', '.') . '%</td>'; // Exibe a porcentagem formatada
                                        echo '</tr>';
                                    }
                                } else if ($buscaGenero['status'] == 'not_found') {
                                    echo '<tr>';
                                    echo '<td colspan="3">' . $buscaGenero['message'] . '</td>';
                                    echo '</tr>';
                                } else if ($buscaGenero['status'] == 'error') {
                                    echo '<tr>';
                                    echo '<td colspan="3">' . $buscaGenero['message'] . ' | Código do erro: ' . $buscaGenero['error_id'] . '</td>';
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
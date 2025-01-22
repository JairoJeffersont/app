<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\ClienteController;
use GabineteDigital\Controllers\PessoaController;
use GabineteDigital\Controllers\PessoaTipoController;
use GabineteDigital\Controllers\PessoaProfissaoController;


$pessoaController = new PessoaController();
$clienteController = new ClienteController;
$pessoaTipoController = new PessoaTipoController();
$pessoaProfissaoController = new PessoaProfissaoController();

$buscaCliente = $clienteController->buscarCliente('cliente_id', $_SESSION['usuario_cliente']);

$estadoDep = $_SESSION['cliente_deputado_estado'];

$estado = (isset($_GET['estado']) && $_GET['estado'] !== 'null') ? $estadoDep : null;


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
                    <p class="card-text mb-0">Nesta seção, é possível ver informações sobre as pessoas de interesse do mandato.</p>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <input type="hidden" name="secao" value="estatisticas" />

                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="estado" required>
                                        <option value="null" <?php echo $estado === null ? 'selected' : ''; ?>>Todos os estados</option>
                                        <option value="<?php echo $estadoDep ?>" <?php echo $estado === $estadoDep ? 'selected' : ''; ?>>Somente <?php echo $estadoDep ?></option>
                                    </select>
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
                <div class="card-header bg-primary text-white px-2 py-1">Gênero</div>
                <div class="card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Gênero</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaGenero = $pessoaController->buscarSexo($estado, $_SESSION['usuario_cliente']);
                                $totalPessoas = $buscaGenero['dados'][0]['total'];
                                if ($buscaGenero['status'] == 'success') {
                                    foreach ($buscaGenero['dados'] as $genero) {
                                        $porcentagem = ($genero['contagem'] / $totalPessoas) * 100;
                                        echo '<tr>';
                                        echo '<td>' . $genero['pessoa_sexo'] . '</td>';
                                        echo '<td>' . $genero['contagem'] . '</td>';
                                        echo '<td>' . number_format($porcentagem, 2, ',', '.') . '%</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-secondary text-white px-2 py-1">Profissões</div>
                <div class="card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Profissão</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaGenero = $pessoaController->buscarProfissao($estado, $_SESSION['usuario_cliente']);
                                $totalPessoas = $buscaGenero['dados'][0]['total'];
                                if ($buscaGenero['status'] == 'success') {
                                    foreach ($buscaGenero['dados'] as $genero) {
                                        $porcentagem = ($genero['contagem'] / $totalPessoas) * 100; // Calcula a porcentagem
                                        echo '<tr>';
                                        echo '<td>' . $genero['pessoas_profissoes_nome'] . '</td>';
                                        echo '<td>' . $genero['contagem'] . '</td>';
                                        echo '<td>' . number_format($porcentagem, 2, ',', '.') . '%</td>'; // Exibe a porcentagem formatada
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-success text-white px-2 py-1">Municípios</div>
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
                                $buscaGenero = $pessoaController->buscarMunicipio($estado, $_SESSION['usuario_cliente']);
                                $totalPessoas = $buscaGenero['dados'][0]['total'];
                                if ($buscaGenero['status'] == 'success') {
                                    foreach ($buscaGenero['dados'] as $genero) {
                                        $porcentagem = ($genero['contagem'] / $totalPessoas) * 100; // Calcula a porcentagem
                                        echo '<tr>';
                                        echo '<td>' . $genero['pessoa_municipio'] . '/'.$genero['pessoa_estado'].'</td>';
                                        echo '<td>' . $genero['contagem'] . '</td>';
                                        echo '<td>' . number_format($porcentagem, 2, ',', '.') . '%</td>'; // Exibe a porcentagem formatada
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
    </div>
</div>
</div>
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
$estadoDep = ($buscaCliente['status'] == 'success') ? $buscaCliente['dados'][0]['cliente_deputado_estado'] : '';

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
        </div>
    </div>
</div>
</div>
<?php

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

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
            <?php

            if ($_SESSION['cliente_deputado_tipo'] == 'Deputado Federal') {
                include 'proposicoes-dep.php';
            } else if ($_SESSION['cliente_deputado_tipo'] == 'Senador') {
                include 'proposicoes-senado.php';
            } else if ($_SESSION['cliente_deputado_tipo'] == 'Prefeito' || $_SESSION['cliente_deputado_tipo'] == 'Governador') {
                header('Location: ?secao=home');
            } else {
                include 'proposicoes-geral.php';
            }

            ?>

        </div>
    </div>
</div>
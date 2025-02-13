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
            } else {
                echo ' <div class="card mb-2 card-description text-center">
                       
                        <div class="card-body p-4">
                            <img src="public/img/fatal_error.png" alt="Em implementação" width="100" class="img-fluid mb-2">
                            <h4 class="text-muted">Esta seção está em construção</h4>

                            <p class="card-text"> Somente proposições de deputados federais estão disponíveis</p>
                        </div>
                    </div>
';
            }

            ?>

        </div>
    </div>
</div>
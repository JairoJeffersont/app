<?php

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

use GabineteDigital\Controllers\EmendaController;
use GabineteDigital\Controllers\EmendasObjetivosController;
use GabineteDigital\Controllers\EmendasStatusController;
use GabineteDigital\Controllers\OrgaoController;

$emendaController = new EmendaController();
$emendasStatusController = new EmendasStatusController();
$emendasObjetivosController = new EmendasObjetivosController();
$orgaosController = new OrgaoController();

$estadoDep = $_SESSION['cliente_deputado_estado'];
$estado = (isset($_GET['estado']) && $_GET['estado'] !== 'null') ? $_SESSION['cliente_deputado_estado'] : null;


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
                    <p class="card-text mb-0">Nesta seção, você pode consultar estatísticas sobre as emendas parlamentares. É possível filtrar os dados por estado para obter informações mais específicas.</p>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1">Status</div>
                <div class="card-body p-2">
                    <form class="row g-2 form_custom mb-2" method="GET" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="secao" value="estatisticas-emendas" />
                        <input type="hidden" name="estado" value="<?php echo $_SESSION['cliente_deputado_estado'] ?>" />

                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="status" required>
                                <option value="0">Todos os status</option>
                                <?php
                                $emendasStatus = $emendasStatusController->listarEmendasStatus($_SESSION['usuario_cliente']);
                                if ($emendasStatus['status'] == 'success') {
                                    foreach ($emendasStatus['dados'] as $status) {
                                        if ($status['emendas_status_id'] == $statusGet) {
                                            echo '<option value="' . $status['emendas_status_id'] . '" selected>' . $status['emendas_status_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $status['emendas_status_id'] . '">' . $status['emendas_status_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>

                    <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                        <thead>
                            <tr>
                                <th scope="col">Status</th>
                                <th scope="col">Quantidade</th>
                                <th scope="col">%</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
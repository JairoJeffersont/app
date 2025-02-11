<?php
ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\AgendaController;
use GabineteDigital\Controllers\AgendaSituacaoController;
use GabineteDigital\Controllers\AgendaTipoController;

$agendaController = new AgendaController();
$agendaTipoController = new AgendaTipoController();
$agendaSituacaoControllr = new AgendaSituacaoController();

$dataGet = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
$tipoGet = (isset($_GET['tipo']) && $_GET['tipo'] !== 'null') ? $_GET['tipo'] : null;
$situacaoGet = (isset($_GET['situacao']) && $_GET['situacao'] !== 'null') ? $_GET['situacao'] : null;

?>

<style>
    body {
        background-image: url(public/img/print_bg.jpeg);
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    @media print {

        @page {
            margin: 0;
            size: A4 portrait;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-image: url(public/img/print_bg.jpeg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        header,
        footer {
            display: none !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    }
</style>

<script>
    window.onload = function() {
        window.print();

        window.onafterprint = function() {
          window.close();
        };
    };
</script>

<div class="container-fluid p-2">
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="card" style="background: none; border: none;">
                <div class="card-body text-center" style="background: none;">
                    <img src="public/img/brasaooficialcolorido.png" class="img-fluid mb-2" style="width: 150px;" />
                    <h5 class="card-title mb-2"><?php echo $_SESSION['cliente_deputado_tipo'] ?> <?php echo $_SESSION['cliente_deputado_nome'] ?></h5>
                    <p class="card-text" style="font-size: 1.4em;">Agenda de compromissos - <?php echo date('d/m', strtotime($dataGet)) ?></p>
                </div>
            </div>
        </div>
    </div>


    <?php
    $buscaAgendas = $agendaController->listarAgendas($dataGet, $tipoGet, $situacaoGet, $_SESSION['usuario_cliente']);

    if ($buscaAgendas['status'] == 'success') {
        echo ' <div class="accordion" id="accordionPanelsStayOpenExample">';
        foreach ($buscaAgendas['dados'] as $agenda) {
            echo '<div class="row d-flex mb-2 justify-content-center">
                        <div class="col-10">
                            <div class="card" style="background: none; ">
                                <div class="card-body border" style="background: none;">
                                    <h6 class="card-title">'.date('H:i', strtotime($agenda['agenda_data'])).'- '.$agenda['agenda_titulo'].'</h6>
                                    <p class="card-text mb-2"><em>'.$agenda['agenda_informacoes'].'</em></p>
                                    <p class="card-text mb-2">'.$agenda['agenda_local'].' - '.$agenda['agenda_estado'].'</p>
                                    <p class="card-text mb-0">'.$agenda['agenda_tipo_nome'].'</p>
                                    <p class="card-text mb-0">'.$agenda['agenda_situacao_nome'].'</p>
                                </div>
                            </div>
                        </div>
                    </div>';
        }
    } else {
        echo '<p class="card-text">Nenhuma agenda para o dia <b>' . date('d/m', strtotime($dataGet)) . '</b></p>';
    }
    ?>




</div>
<?php

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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-newspaper"></i> Adicionar Compromisso</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar os compromissos, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'agenda_titulo' => htmlspecialchars($_POST['agenda_titulo'], ENT_QUOTES, 'UTF-8'),
                            'agenda_situacao' => htmlspecialchars($_POST['agenda_situacao'], ENT_QUOTES, 'UTF-8'),
                            'agenda_tipo' => htmlspecialchars($_POST['agenda_tipo'], ENT_QUOTES, 'UTF-8'),
                            'agenda_data' => htmlspecialchars($_POST['agenda_data'], ENT_QUOTES, 'UTF-8'),
                            'agenda_local' => htmlspecialchars($_POST['agenda_local'], ENT_QUOTES, 'UTF-8'),
                            'agenda_estado' => htmlspecialchars($_POST['agenda_estado'], ENT_QUOTES, 'UTF-8'),
                            'agenda_informacoes' => htmlspecialchars($_POST['agenda_informacoes'], ENT_QUOTES, 'UTF-8'),
                            'agenda_criada_por' => $_SESSION['usuario_id'],
                            'agenda_cliente' => $_SESSION['usuario_cliente'],
                        ];

                        $result = $agendaController->criarAgenda($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaAgendas = $agendaController->listarAgendas($dataGet, $tipoGet, $situacaoGet, $_SESSION['usuario_cliente']);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_titulo" placeholder="Titulo do compromisso" required>
                        </div>

                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="agenda_situacao" required>

                                <?php
                                $buscaSituacoes = $agendaSituacaoControllr->listarAgendaSituacoes($_SESSION['usuario_cliente']);
                                if ($buscaSituacoes['status'] == 'success') {
                                    foreach ($buscaSituacoes['dados'] as $situacao) {
                                        if ($situacao['agenda_situacao_id'] == 1) {
                                            echo '<option value="' . $situacao['agenda_situacao_id'] . '" selected>' . $situacao['agenda_situacao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $situacao['agenda_situacao_id'] . '">' . $situacao['agenda_situacao_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Nova situação + </option>
                            </select>
                        </div>

                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="agenda_tipo" required>

                                <?php
                                $buscaTipos = $agendaTipoController->listarAgendaTipos($_SESSION['usuario_cliente']);
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['dados'] as $tipo) {
                                        if ($tipo['agenda_tipo_id'] == 1) {
                                            echo '<option value="' . $tipo['agenda_tipo_id'] . '" selected>' . $tipo['agenda_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['agenda_tipo_id'] . '">' . $tipo['agenda_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Nova tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="datetime-local" class="form-control form-control-sm" name="agenda_data" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_local" placeholder="Local da agenda" required>
                        </div>

                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="agenda_estado" required>
                                <option value="DF">Brasília</option>
                                <option value="<?php echo $_SESSION['cliente_deputado_estado'] ?>">Estado</option>
                                <option value="Outro">Outro</option>

                            </select>
                        </div>

                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="agenda_informacoes" rows="10" placeholder="Informações da agenda" required></textarea>
                        </div>


                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-2 no-print">
                <div class="card-body p-2">
                    <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-1 col-12">
                            <input type="hidden" name="secao" value="agendas" />
                            <input type="date" class="form-control form-control-sm" name="data" value="<?php echo $dataGet ?>">
                        </div>
                        <div class="col-md-2 col-5">
                            <select class="form-select form-select-sm" name="tipo" required>
                                <option value="null">Tudo</option>
                                <?php
                                $buscaTipos = $agendaTipoController->listarAgendaTipos($_SESSION['usuario_cliente']);
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['dados'] as $tipo) {
                                        if ($tipo['agenda_tipo_id'] == $tipoGet) {
                                            echo '<option value="' . $tipo['agenda_tipo_id'] . '" selected>' . $tipo['agenda_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['agenda_tipo_id'] . '">' . $tipo['agenda_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>

                            </select>
                        </div>

                        <div class="col-md-2 col-5">
                            <select class="form-select form-select-sm" name="situacao" required>
                                <option value="null">Tudo</option>

                                <?php
                                $buscaSituacoes = $agendaSituacaoControllr->listarAgendaSituacoes($_SESSION['usuario_cliente']);
                                if ($buscaSituacoes['status'] == 'success') {
                                    foreach ($buscaSituacoes['dados'] as $situacao) {
                                        if ($situacao['agenda_situacao_id'] == $situacaoGet) {
                                            echo '<option value="' . $situacao['agenda_situacao_id'] . '" selected>' . $situacao['agenda_situacao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $situacao['agenda_situacao_id'] . '">' . $situacao['agenda_situacao_nome'] . '</option>';
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
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    $buscaAgendas = $agendaController->listarAgendas($dataGet, $tipoGet, $situacaoGet, $_SESSION['usuario_cliente']);

                    if ($buscaAgendas['status'] == 'success') {
                        echo ' <div class="accordion" id="accordionPanelsStayOpenExample">';
                        foreach ($buscaAgendas['dados'] as $agenda) {
                            echo '
                                    <div class="accordion-item">
                                            <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" style="font-size: 0.5em" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $agenda['agenda_id'] . '" aria-expanded="true" aria-controls="panelsStayOpen-collapse' . $agenda['agenda_id'] . '">
                                                ' . date('H:i', strtotime($agenda['agenda_data'])) . ' | ' . $agenda['agenda_titulo'] . '
                                            </button>
                                            </h2>
                                            <div id="panelsStayOpen-collapse' . $agenda['agenda_id'] . '" class="accordion-collapse collapse">
                                                <div class="accordion-body" style="font-size: 0.9em">
                                                    <p class="card-text mb-1"><i class="bi bi-arrow-right-short"></i> ' . $agenda['agenda_tipo_nome'] . '</p>
                                                    <p class="card-text mb-3"><i class="bi bi-arrow-right-short"></i> <b>' . $agenda['agenda_situacao_nome'] . '</b></p>
                                                    <p class="card-text mb-3"><i class="bi bi-arrow-right-short"></i> ' . $agenda['agenda_local'] . ' - ' . $agenda['agenda_estado'] . '</p>
                                                    <p class="card-text mb-0"><i class="bi bi-arrow-right-short"></i> ' . $agenda['agenda_informacoes'] . '</p>
                                                </div>
                                            </div>
                                        </div>
                                    ';
                        }
                        echo '</div>';
                    } else {
                        echo '<p class="card-text">' . $buscaAgendas['message'] . '</p>';
                    }
                    ?>
                </div>
            </div>


        </div>
    </div>
</div>
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

$agendaGet = $_GET['id'];

$buscaAgenda = $agendaController->buscarAgenda('agenda_id', $agendaGet);

if ($buscaAgenda['status'] == 'not_found' || $buscaAgenda['status'] == 'error') {
    header('Location: ?secao=agendas');
}

?>
<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=agendas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-newspaper"></i> Editar Compromisso</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar os compromissos, garantindo a organização correta dessas informações no sistema.</p>
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


                        print_r($dados);

                        $result = $agendaController->atualizarAgenda($agendaGet, $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaAgenda = $agendaController->buscarAgenda('agenda_id', $agendaGet);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $agendaController->apagarAgenda($agendaGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=agendas');
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_titulo" placeholder="Titulo do compromisso" value="<?php echo $buscaAgenda['dados'][0]['agenda_titulo'] ?>" required>
                        </div>

                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="agenda_situacao" required>

                                <?php
                                $buscaSituacoes = $agendaSituacaoControllr->listarAgendaSituacoes($_SESSION['usuario_cliente']);
                                if ($buscaSituacoes['status'] == 'success') {
                                    foreach ($buscaSituacoes['dados'] as $situacao) {
                                        if ($situacao['agenda_situacao_id'] == $buscaAgenda['dados'][0]['agenda_situacao']) {
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
                                        if ($tipo['agenda_tipo_id'] == $buscaAgenda['dados'][0]['agenda_tipo']) {
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
                            <input type="datetime-local" class="form-control form-control-sm" name="agenda_data" value="<?php echo $buscaAgenda['dados'][0]['agenda_data'] ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_local" placeholder="Local da agenda" value="<?php echo $buscaAgenda['dados'][0]['agenda_titulo'] ?>" required>
                        </div>

                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="agenda_estado" required>
                                <option value="DF" <?php echo $buscaAgenda['dados'][0]['agenda_estado'] == "DF" ? 'selected' : ''; ?>>Brasília</option>
                                <option value="<?php echo $_SESSION['cliente_deputado_estado']; ?>" <?php echo $buscaAgenda['dados'][0]['agenda_estado'] == $_SESSION['cliente_deputado_estado'] ? 'selected' : ''; ?>>Estado</option>
                                <option value="Outro" <?php echo $buscaAgenda['dados'][0]['agenda_estado'] == "Outro" ? 'selected' : ''; ?>>Outro</option>
                            </select>
                        </div>

                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="agenda_informacoes" rows="10" placeholder="Informações da agenda" required><?php echo $buscaAgenda['dados'][0]['agenda_titulo'] ?></textarea>
                        </div>


                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>

                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
</div>
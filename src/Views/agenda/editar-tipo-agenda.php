<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\AgendaTipoController;

$agendaTipoController = new AgendaTipoController;

$tipoGet = $_GET['id'];

$buscaTipo = $agendaTipoController->buscarAgendaTipo('agenda_tipo_id', $tipoGet);

if ($buscaTipo['status'] == 'not_found' || $buscaTipo['status'] == 'error') {
    header('Location: ?secao=tipos-agendas');
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
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=tipos-agendas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-building"></i> Editar Tipo de Agenda</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar os tipos de agendas, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

                        $dados = [
                            'agenda_tipo_nome' => htmlspecialchars($_POST['agenda_tipo_nome'], ENT_QUOTES, 'UTF-8'),
                            'agenda_tipo_descricao' => htmlspecialchars($_POST['agenda_tipo_descricao'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $agendaTipoController->atualizarAgendaTipo($tipoGet, $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaTipo = $agendaTipoController->buscarAgendaTipo('agenda_tipo_id', $tipoGet);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $agendaTipoController->apagarAgendaTipo($tipoGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=tipos-agendas');
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }

                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_tipo_nome" placeholder="Nome do Tipo" value="<?php echo $buscaTipo['dados'][0]['agenda_tipo_nome'] ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_tipo_descricao" placeholder="Descrição" value="<?php echo $buscaTipo['dados'][0]['agenda_tipo_descricao'] ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\AgendaSituacaoController;

$agendaSituacaoController = new AgendaSituacaoController;

$tipoGet = $_GET['id'];

$buscaSituacao = $agendaSituacaoController->buscarAgendaSituacao('agenda_situacao_id', $tipoGet);

if ($buscaSituacao['status'] == 'not_found' || $buscaSituacao['status'] == 'error') {
    header('Location: ?secao=situacoes-agenda');
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
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=situacoes-agendas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-building"></i> Editar Situação de Agenda</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar as situações de agenda, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

                        $dados = [
                            'agenda_situacao_nome' => htmlspecialchars($_POST['agenda_situacao_nome'], ENT_QUOTES, 'UTF-8'),
                            'agenda_situacao_descricao' => htmlspecialchars($_POST['agenda_situacao_descricao'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $agendaSituacaoController->atualizarAgendaSituacao($tipoGet, $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaSituacao = $agendaSituacaoController->buscarAgendaSituacao('agenda_situacao_id', $tipoGet);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $agendaSituacaoController->apagarAgendaSituacao($tipoGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=situacoes-agendas');
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }

                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_situacao_nome" placeholder="Nome da Situação" value="<?php echo $buscaSituacao['dados'][0]['agenda_situacao_nome'] ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_situacao_descricao" placeholder="Descrição" value="<?php echo $buscaSituacao['dados'][0]['agenda_situacao_descricao'] ?>" required>
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

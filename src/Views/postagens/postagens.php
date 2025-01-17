<?php

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

use GabineteDigital\Controllers\PostagemController;

use GabineteDigital\Controllers\PostagemStatusController;

$postagemStatusController = new PostagemStatusController;
$postagemController = new PostagemController;

?>
<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-stickies"></i> Adicionar Postagem</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar as postagens, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    // Verifica se o formulário foi submetido
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'postagem_titulo' => htmlspecialchars($_POST['postagem_titulo'], ENT_QUOTES, 'UTF-8'),
                            'postagem_informacoes' => htmlspecialchars($_POST['postagem_informacoes'], ENT_QUOTES, 'UTF-8'),
                            'postagem_data' => htmlspecialchars($_POST['postagem_data'], ENT_QUOTES, 'UTF-8'),
                            'postagem_midias' => htmlspecialchars($_POST['postagem_midias'], ENT_QUOTES, 'UTF-8'),
                            'postagem_status' => htmlspecialchars($_POST['postagem_status'], ENT_QUOTES, 'UTF-8'),
                            'postagem_criada_por' => $_SESSION['usuario_id'],
                            'postagem_cliente' => $_SESSION['usuario_cliente'],
                        ];

                        // Chama o método para criar a postagem
                        $result = $postagemController->criarPostagem($dados);

                        // Exibe o resultado com base no status da operação
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_titulo" placeholder="Título" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_midias" placeholder="Mídias (facebook, instagram, site...)" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="date" class="form-control form-control-sm" name="postagem_data" value="<?php echo date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <select class="form-select form-select-sm" name="postagem_status" required>
                                <?php
                                $status_postagens = $postagemStatusController->listarPostagensStatus($_SESSION['usuario_cliente']);
                                if ($status_postagens['status'] == 'success') {
                                    foreach ($status_postagens['dados'] as $status) {
                                        if ($status['postagem_status_id'] == 1) {
                                            echo '<option value="' . $status['postagem_status_id'] . '" selected>' . $status['postagem_status_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $status['postagem_status_id'] . '">' . $status['postagem_status_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="postagem_informacoes" placeholder="Informações" rows="4" required></textarea>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Título</th>
                                    <th scope="col">Mídias</th>
                                    <th scope="col">Data</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Criado por - em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php                                
                                $busca = $postagemController->listarPostagens($_SESSION['usuario_cliente']);
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $postagem) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=postagem&id=' . $postagem['postagem_id'] . '">' . $postagem['postagem_titulo'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['postagem_midias'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($postagem['postagem_data'])) . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['postagem_status_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['usuario_nome'] . ' - ' . date('d/m', strtotime($postagem['postagem_criada_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="5">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="5">' . $busca['message'] . ' | Código do erro: ' . $busca['id_erro'] . '</td></tr>';
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
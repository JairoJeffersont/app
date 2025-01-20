<?php

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

use GabineteDigital\Controllers\PostagemController;
use GabineteDigital\Controllers\PostagemStatusController;

use GabineteDigital\Middleware\FileUploader;

$fileUploader = new FileUploader;

$postagemStatusController = new PostagemStatusController;
$postagemController = new PostagemController;

$postagemGet = $_GET['id'];
$buscaPostagem = $postagemController->buscarPostagem('postagem_id', $postagemGet);


if ($buscaPostagem['status'] == 'not_found' || $buscaPostagem['status'] == 'error') {
    header('Location: ?secao=postagens');
}

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=postagens" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-stickies"></i> Editar Postagem</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Pasta para arquivamento da postagem.
                    <p class="card-text mb-0">Salve os arquivos das postagens para arquivar. O arquivo deve ter no máximo <b>200MB</b></p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

                        $dados = [
                            'postagem_titulo' => htmlspecialchars($_POST['postagem_titulo'], ENT_QUOTES, 'UTF-8'),
                            'postagem_informacoes' => htmlspecialchars($_POST['postagem_informacoes'], ENT_QUOTES, 'UTF-8'),
                            'postagem_data' => htmlspecialchars($_POST['postagem_data'], ENT_QUOTES, 'UTF-8'),
                            'postagem_midias' => htmlspecialchars($_POST['postagem_midias'], ENT_QUOTES, 'UTF-8'),
                            'postagem_status' => htmlspecialchars($_POST['postagem_status'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $postagemController->atualizarPostagem($buscaPostagem['dados'][0]['postagem_id'], $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaPostagem = $postagemController->buscarPostagem('postagem_id', $postagemGet);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $resultado = $postagemController->apagarPostagem($postagemGet);

                        if ($resultado['status'] === 'success') {
                            header('Location: ?secao=postagens');
                        } elseif ($resultado['status'] === 'error' || $resultado['status'] === 'invalid_id' || $resultado['status'] === 'delete_conflict') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_titulo" placeholder="Título" value="<?php echo $buscaPostagem['dados'][0]['postagem_titulo'] ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_midias" placeholder="Mídias (facebook, instagram, site...)" value="<?php echo $buscaPostagem['dados'][0]['postagem_midias'] ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="date" class="form-control form-control-sm" name="postagem_data" value="<?php echo $buscaPostagem['dados'][0]['postagem_data'] ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <select class="form-select form-select-sm" name="postagem_status" id="status_postagem" required>

                                <?php
                                $status_postagens = $postagemStatusController->listarPostagensStatus($_SESSION['usuario_cliente']);
                                if ($status_postagens['status'] == 'success') {
                                    foreach ($status_postagens['dados'] as $status) {
                                        if ($status['postagem_status_id'] == $buscaPostagem['dados'][0]['postagem_status']) {
                                            echo '<option value="' . $status['postagem_status_id'] . '" selected>' . $status['postagem_status_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $status['postagem_status_id'] . '">' . $status['postagem_status_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="postagem_informacoes" placeholder="Informações" rows="4" required><?php echo $buscaPostagem['dados'][0]['postagem_informacoes'] ?></textarea>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>

                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_upload'])) {
                        $uploadResult = $fileUploader->uploadFile($buscaPostagem['dados'][0]['postagem_pasta'], $_FILES['arquivo'], ['jpg', 'jpeg', 'png', 'psd', 'ai', 'cdr'], 100, false);

                        if ($uploadResult['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $uploadResult['message'] . '</div>';
                        } else if ($uploadResult['status'] == 'file_not_permited' || $uploadResult['status'] == 'too_big' || $uploadResult['status'] == 'file_exists') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $uploadResult['message'] . '</div>';
                        } else if ($uploadResult['status'] == 'upload_error' || $uploadResult['status'] == 'folder_error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $uploadResult['message'] . '</div>';
                        }
                    }

                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-3 col-12">
                            <input type="file" class="form-control form-control-sm" name="arquivo" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_upload"><i class="bi bi-cloud-arrow-up"></i> Upload</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar_arquivo'])) {
                        $arquivoParaApagar = $_POST['arquivo_para_apagar'] ?? '';
                        $deleteResult = $fileUploader->deleteFile($arquivoParaApagar);

                        $statusClass = $deleteResult['status'] === 'success' ? 'success' : 'danger';
                        echo '<div class="alert alert-' . htmlspecialchars($statusClass) . ' px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">'
                            . htmlspecialchars($deleteResult['message']) . '</div>';
                    }

                    $pasta = $buscaPostagem['dados'][0]['postagem_pasta'] ?? '';

                    if (is_dir($pasta)) {
                        $arquivos = array_filter(scandir($pasta), function ($arquivo) use ($pasta) {
                            return !in_array($arquivo, ['.', '..']) && is_file($pasta . '/' . $arquivo);
                        });

                        // Ordenar os arquivos pela data de criação (do mais novo para o mais antigo)
                        usort($arquivos, function ($a, $b) use ($pasta) {
                            $dataA = filemtime($pasta . '/' . $a);
                            $dataB = filemtime($pasta . '/' . $b);
                            return $dataB - $dataA; // Maior para menor (do mais novo para o mais antigo)
                        });

                        if (!empty($arquivos)) {
                            echo '<table class="table table-hover table-bordered table-striped mb-0 custom-table">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>Nome do Arquivo</th>';
                            echo '<th>Data e Hora</th>';
                            echo '<th>Apagar</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            foreach ($arquivos as $arquivo) {
                                $caminhoArquivo = $pasta . '/' . $arquivo;
                                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                                $dataHora = date('d/m/Y H:i:s', filemtime($caminhoArquivo));

                                $link = '<a href="' . htmlspecialchars($caminhoArquivo) . '" ';
                                $link .= in_array($extensao, ['jpg', 'jpeg', 'png']) ? 'target="_blank">' : 'download>';
                                $link .= htmlspecialchars($arquivo) . '</a>';

                                echo '<tr>';
                                echo '<td>' . $link . '</td>';
                                echo '<td>' . htmlspecialchars($dataHora) . '</td>';
                                echo '<td>';
                                echo '<form method="POST">';
                                echo '<input type="hidden" name="arquivo_para_apagar" value="' . htmlspecialchars($caminhoArquivo) . '">';
                                echo '<button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.8em" name="btn_apagar_arquivo">Apagar</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo '<p class="text-primary mb-0">Nenhum arquivo encontrado na pasta.</p>';
                        }
                    } else {
                        echo '<p class="text-danger mb-0">A pasta especificada não existe.</p>';
                    }
                    ?>

                </div>
            </div>


        </div>
    </div>

    <script>
        $('#status_postagem').change(function() {
            if ($('#status_postagem').val() == '+') {
                if (window.confirm("Você realmente deseja inserir um novo status?")) {
                    window.location.href = "?secao=status-postagens";
                } else {
                    $('#status_postagem').val(1).change();
                }
            }
        });
    </script>
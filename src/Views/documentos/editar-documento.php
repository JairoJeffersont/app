<?php
ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\documentoController;
use GabineteDigital\Controllers\DocumentoTipoController;
use GabineteDigital\Controllers\OrgaoController;

$orgaoController = new OrgaoController();
$documentoController = new DocumentoController();
$tipoDocumento = new DocumentoTipoController();


$documentoGet = $_GET['id'];

$buscaDocumento = $documentoController->buscarDocumento('documento_id', $documentoGet);

if ($buscaDocumento['status'] == 'not_found' || is_integer($documentoGet) || $buscaDocumento['status'] == 'error') {
    header('Location: ?secao=docuemntos');
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
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=documentos" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text"></i> documentos</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e arquivar documentos do sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2 ">
                <div class="card-body p-0">
                    <nav class="navbar navbar-expand bg-body-tertiary p-0 ">
                        <div class="container-fluid p-0">
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link active p-1" aria-current="page" href="#">
                                            <button class="btn btn-primary btn-sm" style="font-size: 0.850em;" id="btn_novo_orgao" type="button"><i class="bi bi-plus-circle-fill"></i> Novo órgão</button>
                                            <button class="btn btn-success btn-sm" style="font-size: 0.850em;" id="btn_novo_tipo" type="button"><i class="bi bi-plus-circle-fill"></i> Novo tipo</button>

                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

                        $dados = [
                            'documento_titulo' => htmlspecialchars($_POST['documento_titulo'], ENT_QUOTES, 'UTF-8'),
                            'documento_resumo' => htmlspecialchars($_POST['documento_resumo'], ENT_QUOTES, 'UTF-8'),
                            'arquivo' =>  $_FILES['arquivo'],
                            'documento_ano' => htmlspecialchars($_POST['documento_ano'], ENT_QUOTES, 'UTF-8'),
                            'documento_tipo' => htmlspecialchars($_POST['documento_tipo'], ENT_QUOTES, 'UTF-8'),
                            'documento_orgao' => htmlspecialchars($_POST['documento_orgao'], ENT_QUOTES, 'UTF-8'),
                        ];

                        $result = $documentoController->atualizarDocumento($documentoGet, $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                            setTimeout(function(){
                                window.location.href = "?secao=documento&id=' . $documentoGet . '";
                            }, 1000);</script>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }


                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $documentoController->apagarDocumento($documentoGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=documentos');
                            exit;
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="documento_titulo" placeholder="Número" value="<?php echo $buscaDocumento['dados'][0]['documento_titulo'] ?>"required>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="text" class="form-control form-control-sm" name="documento_ano" placeholder="Ano" data-mask="0000" value="<?php echo $buscaDocumento['dados'][0]['documento_ano'] ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="documento_resumo" value="<?php echo $buscaDocumento['dados'][0]['documento_resumo'] ?>" placeholder="Resumo" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" id="orgao" name="documento_orgao">
                                <option value="1" selected>Órgão não informado</option>
                                <?php

                                $orgaos = $orgaoController->listarOrgaos(1000, 1, 'asc', 'orgao_nome', null, null, $_SESSION['usuario_cliente']);

                                if ($buscaOrgao['status'] === 'success') {
                                    foreach ($buscaOrgao['dados'] as $orgao) {
                                        if ($orgao['orgao_id'] == $buscaDocumento['dados'][0]['documento_orgao']) {
                                            echo '<option value="' . $orgao['orgao_id'] . '" selected>' . $orgao['orgao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgao['orgao_id'] . '">' . $orgao['orgao_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>

                                <option value="+">Novo órgão + </option>
                            </select>
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="documento_tipo" id="tipo" required>
                                <?php
                                $tipos = $tipoDocumento->listarDocumentosTipos($_SESSION['usuario_cliente']);
                                if ($tipos['status'] == 'success') {
                                    foreach ($tipos['dados'] as $tipo) {
                                        if ($buscaDocumento['dados'][0]['documento_tipo'] == $tipo['documento_tipo_id']) {
                                            echo '<option value="' . $tipo['documento_tipo_id'] . '" selected>' . $tipo['documento_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['documento_tipo_id'] . '">' . $tipo['documento_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="file" class="form-control form-control-sm" name="arquivo" />
                        </div>
                        <div class="col-md-5 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                            <a type="button" href="<?php echo $buscaDocumento['dados'][0]['documento_arquivo'] ?>" download target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-cloud-arrow-down-fill"></i> Download</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-1">
                    <?php
                    $arquivo = $buscaDocumento['dados'][0]['documento_arquivo'];
                    if (file_exists($arquivo)) {
                        echo "<embed src='$arquivo' type='application/pdf' width='100%' height='1000px'>";
                    } else {
                        echo '<center><img src="public/img/loading.gif"/></center>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#orgao').change(function() {
        if ($('#orgao').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo órgão?")) {
                window.location.href = "?secao=orgaos";
            } else {
                $('#orgao').val(1000).change();
            }
        }
    });

    $('#btn_novo_orgao').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo órgão?")) {
            window.location.href = "?secao=orgaos";
        } else {
            return false;
        }
    });


    $('#tipo').change(function() {
        if ($('#tipo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
                window.location.href = "?secao=tipos-documentos";
            } else {
                $('#orgao').val(1000).change();
            }
        }
    });

    $('#btn_novo_tipo').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
            window.location.href = "?secao=tipos-documentos";
        } else {
            return false;
        }
    });
</script>
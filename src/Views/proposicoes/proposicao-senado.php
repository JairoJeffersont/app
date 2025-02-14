<?php

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$proposicaoIdGet = $_GET['id'];

$buscaProposicao = $proposicaoController->buscarDetalheSenado($proposicaoIdGet);

if ($buscaProposicao['status'] == 'success' && !isset($buscaProposicao['dados']['DetalheMateria']['Materia'])) {
    header('Location: ?secao=proposicoes');
}

//print_r($buscaProposicao['dados']['DetalheMateria']['Materia']['IdentificacaoMateria']['CodigoMateria']);


$buscaNota = $notaController->buscarNotaTecnica('nota_proposicao', $proposicaoIdGet);



?>


<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="#" onclick="history.back(-1);" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-newspaper"></i> Proposição</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Nesta seção, você pode consultar informações de uma proposição.</p>
                </div>
            </div>


            <div class="col-12">
                <div class="card mb-2">
                    <div class="card-body card-description">
                        <h5 class="card-title mb-3">
                            <?php echo $buscaProposicao['dados']['DetalheMateria']['Materia']['IdentificacaoMateria']['DescricaoIdentificacaoMateria']; ?>
                            <small><?php echo isset($buscaNota['dados'][0]['nota_proposicao_apelido']) ? ' | ' . $buscaNota['dados'][0]['nota_proposicao_apelido'] : ''; ?></small>

                        </h5>

                        <p class="card-text mb-2"><?php echo $buscaProposicao['dados']['DetalheMateria']['Materia']['DadosBasicosMateria']['EmentaMateria'] ?></p>
                        <hr class="mb-2 mt-0">
                        <p class="card-text mb-1"><i class="bi bi-calendar2-week"></i> Data de apresentação: <?php echo date('d/m/Y', strtotime($buscaProposicao['dados']['DetalheMateria']['Materia']['DadosBasicosMateria']['DataApresentacao'])) ?></p>


                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-2 ">
                    <div class="card-header bg-success text-white px-2 py-1 card-description"> Nota técnica</div>

                    <div class="card-body p-2">

                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                            $dados = [
                                'nota_proposicao' => $proposicaoIdGet,
                                'nota_proposicao_apelido' => htmlspecialchars($_POST['nota_proposicao_apelido'], ENT_QUOTES, 'UTF-8'),
                                'nota_proposicao_resumo' => htmlspecialchars($_POST['nota_proposicao_resumo'], ENT_QUOTES, 'UTF-8'),
                                'nota_texto' => $_POST['nota_texto'],
                                'nota_criada_por' => $_SESSION['usuario_id'],
                                'nota_cliente' => $_SESSION['usuario_cliente']
                            ];

                            $result = $notaController->criarNotaTecnica($dados);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                echo '<script>
                                    setTimeout(() => {
                                        window.location.href = "?secao=proposicao-senado&id=' . $proposicaoIdGet . '";
                                    }, 1000);
                                </script>
                                ';
                            } else if ($result['status'] == 'duplicated' ||  $result['status'] == 'bad_request') {
                                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            } else if ($result['status'] == 'error') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                            }
                        }

                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {


                            $dados = [
                                'nota_proposicao' => $proposicaoIdGet,
                                'nota_proposicao_apelido' => htmlspecialchars($_POST['nota_proposicao_apelido'], ENT_QUOTES, 'UTF-8'),
                                'nota_proposicao_resumo' => htmlspecialchars($_POST['nota_proposicao_resumo'], ENT_QUOTES, 'UTF-8'),
                                'nota_texto' => $_POST['nota_texto'],
                            ];

                            $result = $notaController->atualizarNotaTecnica($buscaNota['dados'][0]['nota_id'], $dados);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                echo '<script>
                                                setTimeout(() => {
                                                    window.location.href = "?secao=proposicao-senado&id=' . $proposicaoIdGet . '";
                                                }, 1000);
                                            </script>
                                            ';
                            } else if ($result['status'] == 'duplicated' ||  $result['status'] == 'bad_request') {
                                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            } else if ($result['status'] == 'error') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                            }
                        }

                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {

                            $result = $notaController->apagarNotaTecnica($buscaNota['dados'][0]['nota_id']);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                echo '<script>
                                                setTimeout(() => {
                                                    window.location.href = "?secao=proposicao-senado&id=' . $proposicaoIdGet . '";
                                                }, 1000);
                                            </script>
                                            ';
                            } else if ($result['status'] == 'error') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                            }
                        }
                        ?>


                        <form class="row g-2 form_custom" method="POST">
                            <div class="col-md-4 col-12">
                                <input type="text" class="form-control form-control-sm" name="nota_proposicao_apelido" value="<?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados'][0]['nota_proposicao_apelido'] : '' ?>" placeholder="Título" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <input type="text" class="form-control form-control-sm" name="nota_proposicao_resumo" placeholder="Resumo" value="<?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados'][0]['nota_proposicao_resumo'] : '' ?>" required>
                            </div>
                            <div class="col-md-2 col-12">
                                <input type="text" class="form-control form-control-sm" disabled value="<?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados'][0]['usuario_nome'] : $_SESSION['usuario_nome'] ?>" required>
                            </div>
                            <div class="col-md-12 col-12">
                                <script>
                                    tinymce.init({
                                        selector: 'textarea',
                                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount fullscreen',
                                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | alignleft aligncenter alignright alignjustify | numlist bullist indent outdent | emoticons charmap | removeformat | fullscreen',
                                        height: 350,
                                        language: 'pt_BR',
                                        content_css: "public/css/tinymce.css",
                                        setup: function(editor) {
                                            editor.on('init', function() {
                                                editor.getBody().style.fontSize = '10pt';
                                            });
                                        }
                                    });
                                </script>
                                <textarea class="form-control form-control-sm" name="nota_texto" placeholder="Texto" rows="10"><?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados'][0]['nota_texto'] : '' ?></textarea>
                            </div>
                            <div class="col-md-6 col-12">
                                <?php

                                if ($buscaNota['status'] == 'success') {
                                    echo '<button type="submit" class="btn btn-primary btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>&nbsp;';
                                    echo '<button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>&nbsp;';
                                    echo '<a href="?secao=imprimir-proposicao&id=' . $proposicaoIdGet . '" target="_blank" type="button" class="btn btn-secondary btn-sm"><i class="bi bi-printer"></i> Imprimir</a>';
                                } else {
                                    echo '<button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>&nbsp;';
                                    echo '<a href="?secao=imprimir-proposicao&id=' . $proposicaoIdGet . '" target="_blank" type="button" class="btn btn-secondary btn-sm"><i class="bi bi-printer"></i> Imprimir</a>';
                                }

                                ?>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
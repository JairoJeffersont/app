<?php

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;
use GabineteDigital\Controllers\ProposicaoTemaController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();
$temaController = new ProposicaoTemaController();

$proposicaoIdGet = $_GET['id'];

$buscaProposicao = $proposicaoController->buscarDetalhe($proposicaoIdGet);

if ($buscaProposicao['status'] == 'error' || empty($buscaProposicao['dados'])) {
    header('location: ?secao=proposicoes');
}

$buscaAutores = $proposicaoController->buscarAutores($proposicaoIdGet);
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
                            <?php
                            echo $buscaProposicao['dados']['siglaTipo'] . ' ' . $buscaProposicao['dados']['numero'] . '/' . $buscaProposicao['dados']['ano'];
                            echo isset($buscaNota['dados'][0]['nota_proposicao_apelido']) ? ' | ' . $buscaNota['dados'][0]['nota_proposicao_apelido'] : '';
                            ?>
                        </h5>

                        <p class="card-text mb-2"><?php echo $buscaProposicao['dados']['ementa'] ?></p>
                        <hr class="mb-2 mt-0">
                        <p class="card-text mb-1"><i class="bi bi-calendar2-week"></i> Data de apresentação: <?php echo date('d/m/Y', strtotime($buscaProposicao['dados']['dataApresentacao'])) ?></p>
                        <p class="card-text mb-2"><i class="bi bi-archive"></i> Situação: <?php echo ($buscaProposicao['dados']['statusProposicao'] == 'Arquivada') ? 'Arquivada' : 'Em tramitação' ?></p>
                        <?php

                        if ($buscaProposicao['dados']['statusProposicao'] == 'Transformado em Norma Jurídica') {
                            echo '<p class="card-text mb-3"><b>Proposição Aprovada</b></p>';
                        }

                        if (!empty($buscaProposicao['dados']['uriPropPrincipal'])) {
                            $buscaApensado = $proposicaoController->buscarDetalhe(basename($buscaProposicao['dados']['uriPropPrincipal']));
                            if ($buscaApensado['status'] == 'success' && !empty($buscaApensado['dados'])) {
                                echo '<p class="card-text mb-0">Essa proposição foi apensada ao: <b><a href="?secao=proposicao&id=' . $buscaApensado['dados']['id'] . '">' . $buscaApensado['dados']['siglaTipo'] . ' ' . $buscaApensado['dados']['numero'] . '/' . $buscaApensado['dados']['ano'] . '</a></b></p>';
                            }
                        } else {
                            echo '<p class="card-text mb-0">Essa proposição não foi apensada ou é a proposição principal</p>';
                        }
                        ?>
                        <hr>
                        <?php

                        if ($buscaAutores['status'] == 'success' && !empty($buscaAutores['dados'])) {
                            echo '<p class="card-text mb-1"><b>Autor(es):</b></p>';

                            $autores = $buscaAutores['dados'];
                            $quantidadeAutores = count($autores);
                            $exibiuSessao = false;

                            foreach ($autores as $autor) {
                                if ($autor['nome'] == $_SESSION['cliente_deputado_nome'] && !$exibiuSessao) {
                                    // Corrigindo a lógica do operador ternário
                                    echo '<p class="card-text mb-1"><i class="bi bi-person-fill"></i> ' .
                                        ($quantidadeAutores == 1 ? $autor['nome'] : $autor['nome'] . ' - Coautor ou subscrição') .
                                        '</p>';
                                    $exibiuSessao = true;
                                    break;
                                }
                            }

                            if ($quantidadeAutores > 1) {
                                echo '<p class="card-text mb-1">Outros autores (' . ($quantidadeAutores - 1) . ')</p>';
                            }
                        }


                        ?>

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
                                'nota_proposicao_tema' => htmlspecialchars($_POST['nota_proposicao_tema'], ENT_QUOTES, 'UTF-8'),
                                'nota_texto' => $_POST['nota_texto'],
                                'nota_criada_por' => $_SESSION['usuario_id'],
                                'nota_cliente' => $_SESSION['usuario_cliente']
                            ];

                            $result = $notaController->criarNotaTecnica($dados);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                echo '<script>
                                    setTimeout(() => {
                                        window.location.href = "?secao=proposicao&id=' . $proposicaoIdGet . '";
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
                                'nota_proposicao_tema' => htmlspecialchars($_POST['nota_proposicao_tema'], ENT_QUOTES, 'UTF-8'),
                                'nota_texto' => $_POST['nota_texto'],
                            ];

                            $result = $notaController->atualizarNotaTecnica($buscaNota['dados'][0]['nota_id'], $dados);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                echo '<script>
                                                setTimeout(() => {
                                                    window.location.href = "?secao=proposicao&id=' . $proposicaoIdGet . '";
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
                                                    window.location.href = "?secao=proposicao&id=' . $proposicaoIdGet . '";
                                                }, 1000);
                                            </script>
                                            ';
                            } else if ($result['status'] == 'error') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                            }
                        }
                        ?>


                        <form class="row g-2 form_custom" method="POST">
                            <div class="col-md-3 col-12">
                                <input type="text" class="form-control form-control-sm" name="nota_proposicao_apelido" value="<?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados'][0]['nota_proposicao_apelido'] : '' ?>" placeholder="Título" required>
                            </div>
                            <div class="col-md-5 col-12">
                                <input type="text" class="form-control form-control-sm" name="nota_proposicao_resumo" placeholder="Resumo" value="<?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados'][0]['nota_proposicao_resumo'] : '' ?>" required>
                            </div>
                            <div class="col-md-2 col-12">
                                <select class="form-control form-control-sm" name="nota_proposicao_tema" id="nota_proposicao_tema" required>

                                    <?php
                                    $buscaTema = $temaController->listarProposicoesTemas($_SESSION['usuario_cliente']);
                                    if ($buscaTema['status'] == 'success') {
                                        foreach ($buscaTema['dados'] as $tema) {

                                            if (empty($buscaNota['dados'][0]['nota_proposicao_tema'])) {
                                                if ($tema['proposicao_tema_id'] == 21) {
                                                    echo '<option value="' . $tema['proposicao_tema_id'] . '" selected>' . $tema['proposicao_tema_nome'] . '</option>';
                                                } else {
                                                    echo '<option value="' . $tema['proposicao_tema_id'] . '">' . $tema['proposicao_tema_nome'] . '</option>';
                                                }
                                            } else {
                                                if ($buscaNota['dados'][0]['nota_proposicao_tema'] == $tema['proposicao_tema_id']) {
                                                    echo '<option value="' . $tema['proposicao_tema_id'] . '" selected>' . $tema['proposicao_tema_nome'] . '</option>';
                                                } else {
                                                    echo '<option value="' . $tema['proposicao_tema_id'] . '">' . $tema['proposicao_tema_nome'] . '</option>';
                                                }
                                            }
                                        }
                                    }

                                    ?>

                                    <option value="+">Novo Tema</option>


                                </select>
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

            <div class="col-12">
                <div class="card mb-2">
                    <div class="card-body card-description">
                        <div class="accordion" id="accordionPanelsStayOpenExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" style="font-size: 0.5em" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                        <i class="bi bi-file-text"></i> &nbsp; &nbsp;Ver inteiro teor
                                    </button>
                                </h2>
                                <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <?php

                                        if (isset($buscaProposicao['dados']['urlInteiroTeor'])) {
                                            echo '<embed src="' . $buscaProposicao['dados']['urlInteiroTeor'] . '" type="application/pdf" width="100%" height="1000px">';
                                        } else {
                                            echo '<p class="card-text">Documento não disponível</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1"><i class="bi bi-fast-forward-btn"></i> Tramitações</div>

                <div class="card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Despacho</th>
                                    <th scope="col">Órgão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaTramitacoes = $proposicaoController->buscarTramitacoes($buscaProposicao['dados']['id']);

                                if ($buscaTramitacoes['status'] == 'success' && is_array($buscaTramitacoes['dados'])) {
                                    $itens = 10;
                                    $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

                                    usort($buscaTramitacoes['dados'], function ($a, $b) {
                                        return strtotime($b['dataHora']) - strtotime($a['dataHora']);
                                    });

                                    $totalRegistros = count($buscaTramitacoes['dados']);
                                    $totalPagina = ceil($totalRegistros / $itens);

                                    $offset = ($pagina - 1) * $itens;

                                    foreach (array_slice($buscaTramitacoes['dados'], $offset, $itens) as $tramitacao) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y H:i', strtotime($tramitacao['dataHora'])) . '</td>';
                                        echo '<td>' . htmlspecialchars($tramitacao['despacho']);

                                        if (isset($tramitacao['url'])) {
                                            echo ' - <a href="' . $tramitacao['url'] . '" target="_blank">ver documento</a>';
                                        }

                                        echo '</td>';
                                        echo '<td>' . htmlspecialchars($tramitacao['siglaOrgao']) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaTramitacoes['status'] == 'error') {
                                    echo '<p class="card-text">' . $buscaTramitacoes['message'] . '</p>';
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                    <?php
                    if ($totalPagina > 0 && $totalPagina != 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                        echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=proposicao&id=' . $proposicaoIdGet . '&pagina=1">Primeira</a></li>';

                        for ($i = 1; $i < $totalPagina - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicao&id=' . $proposicaoIdGet . '&pagina=' . $pageNumber . '">' . $pageNumber . '</a></li>';
                        }

                        echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=proposicao&id=' . $proposicaoIdGet . '&pagina=' . $totalPagina . '">Última</a></li>';
                        echo '</ul>';
                    }
                    ?>

                </div>
            </div>


        </div>
    </div>
</div>
<script>
    $('#nota_proposicao_tema').change(function() {
        if ($('#nota_proposicao_tema').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo tema?")) {
                window.location.href = "?secao=temas-proposicoes";
            } else {
                $('#nota_proposicao_tema').val(1000).change();
            }
        }
    });
</script>
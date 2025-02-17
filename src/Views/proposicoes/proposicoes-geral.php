<?php


ob_start();

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$anoGet = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$autorGet = $_SESSION['cliente_deputado_nome'];
$tipoget = isset($_GET['tipo']) ? $_GET['tipo'] : 'pl';
$itensGet = isset($_GET['itens']) ? (int)$_GET['itens'] : 10;
$paginaGet = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;




?>

<div class="card mb-2 card-description">
    <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-newspaper"></i> Proposições | <?php echo $_SESSION['cliente_deputado_tipo'] ?></div>
    <div class="card-body p-2">
        <p class="card-text mb-2">Nesta seção, você pode pesquisar pelas proposições do deputado, facilitando o acesso às informações relevantes de forma rápida e organizada.</p>
        <p class="card-text mb-0">Preencha todos os campos para inserir uma proposição no sistema.</p>

    </div>
</div>



<div class="card shadow-sm mb-2 no-print">
    <div class="card-body p-2">

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

            $dados = [
                'proposicao_numero' => htmlspecialchars($_POST['proposicao_numero'], ENT_QUOTES, 'UTF-8'),
                'proposicao_tipo' => htmlspecialchars($_POST['proposicao_tipo'], ENT_QUOTES, 'UTF-8'),
                'proposicao_apresentacao' => date('Y-m-d', strtotime($_POST['proposicao_apresentacao'])),
                'proposicao_arquivada' => $_POST['proposicao_arquivada'],
                'proposicao_ano' => date('Y', strtotime($_POST['proposicao_apresentacao'])),
                'proposicao_titulo' => $_POST['proposicao_tipo'] . ' ' . $_POST['proposicao_numero'] . '/' . date('Y', strtotime($_POST['proposicao_apresentacao'])),
                'proposicao_aprovada' => $_POST['proposicao_aprovada'],
                'proposicao_ementa' => htmlspecialchars($_POST['proposicao_ementa'], ENT_QUOTES, 'UTF-8'),
                'proposicao_criada_por' => $_SESSION['usuario_id'],
                'proposicao_cliente' => $_SESSION['usuario_cliente'],
                'proposicao_autor' => $_SESSION['cliente_deputado_nome']
            ];

            $result = $proposicaoController->criarProposicao($dados);

            if ($result['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
            } else if ($result['status'] == 'duplicated' ||  $result['status'] == 'bad_request') {
                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
            } else if ($result['status'] == 'error') {
                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
            }
        }

        ?>


        <form class="row g-2 form_custom no-print" id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
            <div class="col-md-1 col-12">
                <input type="text" class="form-control form-control-sm" name="proposicao_numero" placeholder="Número da proposição" required>
            </div>
            <div class="col-md-2 col-12">
                <select class="form-control form-control-sm" name="proposicao_tipo" required>
                    <option value="">Selecione o tipo de proposição</option>
                    <option value="PL">Projeto de Lei</option>
                    <option value="PR">Projeto de Resolução</option>
                    <option value="PDC">Projeto de Decreto Legislativo</option>
                    <option value="PLP">Projeto de Lei Complementar</option>
                    <option value="REQ">Requerimento</option>
                </select>
            </div>
            <div class="col-md-1 col-12">
                <input type="text" class="form-control form-control-sm" name="proposicao_apresentacao" data-mask=00/00/0000 placeholder="Data da apresentação" required>
            </div>
            <div class="col-md-2 col-12">
                <select class="form-control form-control-sm" name="proposicao_arquivada" required>
                    <option value="1">Arquivada</option>
                    <option value="0" selected>Tramitando</option>
                </select>
            </div>
            <div class="col-md-2 col-12">
                <select class="form-control form-control-sm" name="proposicao_aprovada" required>
                    <option value="1">Aprovada? (sim)</option>
                    <option value="0" selected>Aprovada? (não)</option>
                </select>
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
                <textarea class="form-control form-control-sm" name="proposicao_ementa" placeholder="Texto" placeholder="Ementa da proposição" rows="10"></textarea>
            </div>

            <div class="col-md-3 col-12">
                <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
            </div>
        </form>
    </div>
</div>

<div class="col-12">
    <div class="card shadow-sm mb-2">
        <div class="card-body p-2">
            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                <div class="col-md-1 col-2">
                    <input type="hidden" name="secao" value="proposicoes" />
                    <input type="text" class="form-control form-control-sm" name="ano" data-mask="0000" value="<?php echo $anoGet ?>">
                </div>
                <div class="col-md-1 col-10">
                    <select class="form-select form-select-sm" name="tipo" required>
                        <option value="pl" <?php echo $tipoget == 'pl' ? 'selected' : ''; ?>>Projeto de lei</option>
                        <option value="req" <?php echo $tipoget == 'req' ? 'selected' : ''; ?>>Requerimento</option>
                        <option value="pec" <?php echo $tipoget == 'pec' ? 'selected' : ''; ?>>PEC</option>

                    </select>
                </div>

                <div class="col-md-1 col-4">
                    <select class="form-select form-select-sm" name="itens" required>
                        <option value="5" <?php echo $itensGet == 5 ? 'selected' : ''; ?>>5 itens</option>
                        <option value="10" <?php echo $itensGet == 10 ? 'selected' : ''; ?>>10 itens</option>
                        <option value="25" <?php echo $itensGet == 25 ? 'selected' : ''; ?>>25 itens</option>
                        <option value="50" <?php echo $itensGet == 50 ? 'selected' : ''; ?>>50 itens</option>
                    </select>
                </div>

                <div class="col-md-1 col-2">
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
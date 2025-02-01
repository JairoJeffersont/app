<?php

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\OficioController;
use GabineteDigital\Controllers\OrgaoController;

$orgaoController = new OrgaoController();
$oficioController = new OficioController();

$ano_busca = (isset($_GET['busca_ano'])) ? $_GET['busca_ano'] : date('Y');
$termo = (isset($_GET['termo'])) ? $_GET['termo'] : '';

$busca = $oficioController->listarOficios($ano_busca, $termo, $_SESSION['usuario_cliente']);

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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-archive"></i> Arquivar ofício</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Seção para arquivamento de ofícios.
                    <p class="card-text mb-0">Todos os campos são <b>obrigatórios</b>. O arquivo deve ser em <b>PDF</b> e ter até <b>5mb</b></p>
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
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $dados = [
                            'oficio_titulo' => htmlspecialchars($_POST['oficio_titulo'], ENT_QUOTES, 'UTF-8') . '/' . htmlspecialchars($_POST['oficio_ano'], ENT_QUOTES, 'UTF-8'),
                            'oficio_resumo' => htmlspecialchars($_POST['oficio_resumo'], ENT_QUOTES, 'UTF-8'),
                            'arquivo' =>  $_FILES['arquivo'],
                            'oficio_ano' => htmlspecialchars($_POST['oficio_ano'], ENT_QUOTES, 'UTF-8'),
                            'oficio_orgao' => htmlspecialchars($_POST['oficio_orgao'], ENT_QUOTES, 'UTF-8'),
                            'oficio_criado_por' => $_SESSION['usuario_id'],
                            'oficio_cliente' => $_SESSION['usuario_cliente']
                        ];

                        $result = $oficioController->criarOficio($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'file_not_permited' || $result['status'] == 'too_big' ||  $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="oficio_titulo" placeholder="Titulo (Ex. OF 0000)" data-mask="OF 000" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="number" class="form-control form-control-sm" name="oficio_ano" data-mask=0000 value="<?php echo $ano_busca ?>">

                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="oficio_orgao" id="orgao" required>
                                <option value="1">Órgão não informado</option>
                                <?php
                                $orgaos = $orgaoController->listarOrgaos(1000, 1, 'asc', 'orgao_nome', null, null, $_SESSION['usuario_cliente']);
                                if ($orgaos['status'] == 'success') {
                                    foreach ($orgaos['dados'] as $orgao) {
                                        echo '<option value="' . $orgao['orgao_id'] . '">' . $orgao['orgao_nome'] . '</option>';
                                    }
                                }
                                ?>
                                <option value="+">Novo órgão + </option>
                            </select>
                        </div>
                        <div class="col-md-5 col-12">
                            <input type="file" class="form-control form-control-sm" name="arquivo" required>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="oficio_resumo" rows="5" placeholder="Resumo do ofício"></textarea>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-1 col-3">
                                    <input type="hidden" name="secao" value="oficios" />
                                    <input type="number" class="form-control form-control-sm" name="busca_ano" data-mask=0000 value="<?php echo $ano_busca ?>">

                                </div>
                                <div class="col-md-3 col-7">
                                    <input type="text" class="form-control form-control-sm" name="termo" value="<?php echo $termo ?>" placeholder="Buscar...">
                                </div>
                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Resumo</th>
                                    <th scope="col">Órgão</th>
                                    <th scope="col">Criado por - em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $busca = $oficioController->listarOficios($ano_busca, $termo, $_SESSION['usuario_cliente']);

                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $oficio) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=oficio&id=' . $oficio['oficio_id'] . '">' . $oficio['oficio_titulo'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $oficio['oficio_resumo'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $oficio['orgao_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $oficio['usuario_nome'] . ' - ' . date('d/m', strtotime($oficio['oficio_criado_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="4">Erro ao carregar os dados. ' . (isset($busca['error_id']) ? ' | Código do erro: ' . $busca['error_id'] : '') . '</td></tr>';
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
</script>
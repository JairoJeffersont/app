<?php

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

use GabineteDigital\Controllers\EmendaController;
use GabineteDigital\Controllers\EmendasObjetivosController;
use GabineteDigital\Controllers\EmendasStatusController;
use GabineteDigital\Controllers\OrgaoController;

$emendaController = new EmendaController();

$emendas = $emendaController->listarEmendas($_SESSION['usuario_cliente']);
$emendasStatusController = new EmendasStatusController();
$emendasObjetivosController = new EmendasObjetivosController();
$orgaosController = new OrgaoController();
?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">

            <!-- Botão de Navegação -->
            <div class="card mb-2">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>

            <!-- Adicionar Emenda -->
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-cash-stack"></i> Adicionar Emenda</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Todos os campos são obrigatórios.</p>
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
                                            <button class="btn btn-success btn-sm" style="font-size: 0.850em;" id="btn_novo_objetivo" type="button"><i class="bi bi-plus-circle-fill"></i> Novo objetivo</button>
                                            <button class="btn btn-secondary btn-sm" style="font-size: 0.850em;" id="btn_nova_status" type="button"><i class="bi bi-plus-circle-fill"></i> Nova status</button>
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
                        $dadosEmenda = [
                            'emenda_numero' => htmlspecialchars($_POST['emenda_numero'], ENT_QUOTES, 'UTF-8'),
                            'emenda_valor' => (float) str_replace(',', '.', str_replace('.', '', htmlspecialchars($_POST['emenda_valor'], ENT_QUOTES, 'UTF-8'))),
                            'emenda_descricao' => htmlspecialchars($_POST['emenda_descricao'], ENT_QUOTES, 'UTF-8'),
                            'emenda_status' => htmlspecialchars($_POST['emenda_status'], ENT_QUOTES, 'UTF-8'),
                            'emenda_orgao' => htmlspecialchars($_POST['emenda_orgao'], ENT_QUOTES, 'UTF-8'),
                            'emenda_municipio' => htmlspecialchars($_POST['emenda_municipio'], ENT_QUOTES, 'UTF-8'),
                            'emenda_estado' => htmlspecialchars($_POST['emenda_estado'], ENT_QUOTES, 'UTF-8'),

                            'emenda_objetivo' => htmlspecialchars($_POST['emenda_objetivo'], ENT_QUOTES, 'UTF-8'),
                            'emenda_informacoes' => htmlspecialchars($_POST['emenda_informacoes'], ENT_QUOTES, 'UTF-8'),
                            'emenda_tipo' => htmlspecialchars($_POST['emenda_tipo'], ENT_QUOTES, 'UTF-8'),
                            'emenda_cliente' => $_SESSION['usuario_cliente'],
                            'emenda_criado_por' => $_SESSION['usuario_id']
                        ];

                        $result = $emendaController->criarEmenda($dadosEmenda);

                        if ($result['status'] == 'success') {
                            $emendas = $emendaController->listarEmendas($_SESSION['usuario_cliente']);
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_numero" placeholder="Número da Emenda" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_valor" id="emenda_valor" placeholder="Valor da Emenda (R$)" required>
                        </div>
                        <div class="col-md-8 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_descricao" placeholder="Descrição" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="emenda_status" id="emenda_status" required>
                                <?php

                                $emendasStatus = $emendasStatusController->listarEmendasStatus($_SESSION['usuario_cliente']);
                                if ($emendasStatus['status'] == 'success') {
                                    foreach ($emendasStatus['dados'] as $status) {
                                        if ($status['emendas_status_id'] == 1) {
                                            echo '<option value="' . $status['emendas_status_id'] . '" selected>' . $status['emendas_status_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $status['emendas_status_id'] . '">' . $status['emendas_status_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                                <option value="+">Novo status + </option>
                            </select>
                        </div>
                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="emenda_orgao" id="orgao" required>
                                <option value="1" selected>Órgão não informado</option>
                                <?php

                                $orgaos = $orgaosController->listarOrgaos(1000, 1, 'ASC', 'orgao_nome', null, null, $_SESSION['usuario_cliente']);
                                if ($orgaos['status'] == 'success') {
                                    foreach ($orgaos['dados'] as $status) {
                                        if ($orgaos['orgao_id'] == 1) {
                                            echo '<option value="' . $orgaos['orgao_id'] . '" selected>' . $orgaos['orgao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgaos['orgao_id'] . '">' . $orgaos['orgao_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                                <option value="+">Novo órgão + </option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="estado" name="emenda_estado" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="municipio" name="emenda_municipio" required>
                                <option value="" selected>Município</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12">
                            <select class="form-select form-select-sm" name="emenda_objetivo" id="emenda_objetivo" required>
                                <?php

                                $emendasObjetivos = $emendasObjetivosController->listarEmendasObjetivos($_SESSION['usuario_cliente']);

                                if ($emendasObjetivos['status'] == 'success') {
                                    foreach ($emendasObjetivos['dados'] as $objetivo) {
                                        if ($objetivo['emendas_objetivos_id'] == 1) {
                                            echo '<option value="' . $objetivo['emendas_objetivos_id'] . '" selected>' . $objetivo['emendas_objetivos_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $objetivo['emendas_objetivos_id'] . '">' . $objetivo['emendas_objetivos_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                                <option value="+">Novo objetivo + </option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12">
                            <select class="form-select form-select-sm" name="emenda_tipo" required>
                                <option value="1">Emenda parlamentar</option>
                                <option value="2">Emenda de bancada</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="emenda_informacoes" placeholder="Informações Adicionais. Ex. Ordem de pagamento, códigos gerais..." rows="5" required></textarea>
                        </div>

                        <div class="col-md-3 col-12">
                            <div class="file-upload">

                                <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Listagem das Emendas -->
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Número</th>
                                    <th scope="col">Valor</th>
                                    <th scope="col">Descrição</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Órgão</th>
                                    <th scope="col">Município</th>
                                    <th scope="col">Criado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($emendas['status'] == 'success') {
                                    foreach ($emendas['dados'] as $emenda) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=editar-emenda&id=' . $emenda['emenda_id'] . '">' . $emenda['emenda_numero'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">R$ ' . number_format($emenda['emenda_valor'], 2, ',', '.') . '</td>';
                                        echo '<td>' . $emenda['emenda_descricao'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $emenda['emendas_status_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $emenda['orgao_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $emenda['emenda_municipio'] . ' | ' . $emenda['emenda_estado'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($emenda['emenda_criada_em'])) . ' | ' . $emenda['usuario_nome'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7">' . $emendas['message'] . '</td></tr>';
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
    $(document).ready(function() {
        carregarEstados();
    });

    function carregarEstados() {
        $.getJSON('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome', function(data) {
            const selectEstado = $('#estado');
            selectEstado.empty();
            selectEstado.append('<option value="" selected>UF</option>');
            data.forEach(estado => {
                selectEstado.append(`<option value="${estado.sigla}">${estado.sigla}</option>`);
            });
        });
    }

    function carregarMunicipios(estadoId) {
        $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios?orderBy=nome`, function(data) {
            const selectMunicipio = $('#municipio');
            selectMunicipio.empty();
            selectMunicipio.append('<option value="" selected>Município</option>');
            data.forEach(municipio => {
                selectMunicipio.append(`<option value="${municipio.nome}">${municipio.nome}</option>`);
            });
        });
    }


    $('#estado').change(function() {
        const estadoId = $(this).val();
        if (estadoId) {
            $('#municipio').empty().append('<option value="">Aguarde...</option>');
            carregarMunicipios(estadoId);
        } else {
            $('#municipio').empty().append('<option value="" selected>Município</option>');
        }
    });


    $('#btn_nova_status').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo status?")) {
            window.location.href = "?secao=status-emendas";
        } else {
            return false;
        }
    });

    $('#btn_novo_objetivo').click(function() {
        if (window.confirm("Você realmente deseja inserir uma novo objetivo?")) {
            window.location.href = "?secao=objetivos-emendas";
        } else {
            return false;
        }
    });

    $('#emenda_objetivo').change(function() {
        if ($('#emenda_objetivo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir uma novo objetivo?")) {
                window.location.href = "?secao=objetivos-emendas";
            } else {
                $('#profissao').val(1000).change();
            }
        }
    });

    $('#emenda_status').change(function() {
        if ($('#emenda_status').val() == '+') {
            if (window.confirm("Você realmente deseja inserir uma novo objetivo?")) {
                window.location.href = "?secao=status-emendas";
            } else {
                $('#profissao').val(1000).change();
            }
        }
    });

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
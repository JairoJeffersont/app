<?php

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

use GabineteDigital\Controllers\OrgaoController;
use GabineteDigital\Controllers\OrgaoTipoController;

$orgaoController = new OrgaoController();
$orgaoTipoController = new OrgaoTipoController();


$orgaoGet = (isset($_GET['id'])) ? $_GET['id'] : 0;
$busca = $orgaoController->buscarOrgao('orgao_id', $orgaoGet);

if ($busca['status'] == 'not_found' || is_integer($orgaoGet) || $busca['status'] == 'error') {
    header('Location: ?secao=orgaos');
    exit();
}

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?pagina=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=orgaos" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-building"></i> Editar órgãos/entidades</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar os tipos de órgãos e entidades, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Os campos <b>Nome</b>, <b>email</b>, <b>estado</b> e <b>município</b> são <b>obrigatórios</b></p>
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
                                            <button class="btn btn-success btn-sm" style="font-size: 0.850em;" id="btn_novo_tipo" type="button"><i class="bi bi-plus-circle-fill"></i> Novo tipo</button>
                                            <button class="btn btn-secondary btn-sm" style="font-size: 0.850em;" id="btn_imprimir" type="button"><i class="bi bi-printer-fill"></i> Imprimir</button>
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
                            'orgao_nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
                            'orgao_email' => htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'),
                            'orgao_telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
                            'orgao_endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
                            'orgao_cep' => htmlspecialchars($_POST['cep'], ENT_QUOTES, 'UTF-8'),
                            'orgao_bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
                            'orgao_estado' => htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8'),
                            'orgao_municipio' => htmlspecialchars($_POST['municipio'], ENT_QUOTES, 'UTF-8'),
                            'orgao_tipo' => htmlspecialchars($_POST['tipo'], ENT_QUOTES, 'UTF-8'),
                            'orgao_site' => htmlspecialchars($_POST['site'], ENT_QUOTES, 'UTF-8'),
                            'orgao_informacoes' => htmlspecialchars($_POST['informacoes'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $orgaoController->atualizarOrgao($orgaoGet, $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                            $busca = $orgaoController->buscarOrgao('orgao_id', $orgaoGet);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }


                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $orgaoController->apagarOrgao($orgaoGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=orgaos');
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom " id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" value="<?php echo $busca['dados'][0]['orgao_nome'] ?>" placeholder="Nome " required>
                        </div>
                        <div class="col-md-4 col-6">
                            <input type="email" class="form-control form-control-sm" name="email" placeholder="Email" value="<?php echo $busca['dados'][0]['orgao_email'] ?>" required>
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (somente números)" value="<?php echo $busca['dados'][0]['orgao_telefone'] ?>" data-mask="(00) 00000-0000" maxlength="11">
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="endereco" placeholder="Endereço" value="<?php echo $busca['dados'][0]['orgao_endereco'] ?>">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="bairro" placeholder="Bairro" value="<?php echo $busca['dados'][0]['orgao_bairro'] ?>">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="cep" placeholder="CEP (somente números)" data-mask="00000-000" maxlength="8" value="<?php echo $busca['dados'][0]['orgao_cep'] ?>">
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" id="estado" name="estado" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="municipio" name="municipio" required>
                                <option value="" selected>Município</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <select class="form-select form-select-sm" id="tipo" name="tipo" required>
                                <?php
                                $buscaTipo = $orgaoTipoController->listarOrgaosTipos($_SESSION['usuario_cliente']);
                                if ($buscaTipo['status'] == 'success') {
                                    foreach ($buscaTipo['dados'] as $orgaoTipo) {
                                        if ($orgaoTipo['orgao_tipo_id'] == $busca['dados'][0]['orgao_tipo']) {
                                            echo '<option value="' . $orgaoTipo['orgao_tipo_id'] . '" selected>' . $orgaoTipo['orgao_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgaoTipo['orgao_tipo_id'] . '">' . $orgaoTipo['orgao_tipo_nome'] . '</option>';
                                        }
                                    }
                                } else if ($buscaTipo['status'] == 'empty') {
                                    echo '<option>' . $busca['message'] . '</option>';
                                } else if ($buscaTipo['status'] == 'error') {
                                    echo '<option>' . $busca['message'] . '</option>';
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-9 col-12">
                            <input type="text" class="form-control form-control-sm" name="site" placeholder="Site ou rede sociais" value="<?php echo $busca['dados'][0]['orgao_site'] ?>">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes desse órgão"><?php echo $busca['dados'][0]['orgao_informacoes'] ?></textarea>
                        </div>
                        <div class="col-md-4 col-6">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-floppy-fill"></i> Apagar</button>
                        </div>
                    </form>
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
                if (estado.sigla === "<?php echo $busca['dados'][0]['orgao_estado'] ?>") {
                    setTimeout(function() {
                        selectEstado.append(`<option value="${estado.sigla}" selected>${estado.sigla}</option>`).change();
                    }, 500);

                } else {
                    setTimeout(function() {
                        selectEstado.append(`<option value="${estado.sigla}">${estado.sigla}</option>`);
                    }, 500);
                }
            });
        });
    }

    function carregarMunicipios(estadoId) {
        $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios?orderBy=nome`, function(data) {
            const selectMunicipio = $('#municipio');
            selectMunicipio.empty();
            selectMunicipio.append('<option value="" selected>Município</option>');
            data.forEach(municipio => {
                if (municipio.nome === "<?php echo $busca['dados'][0]['orgao_municipio'] ?>") {
                    selectMunicipio.append(`<option value="${municipio.nome}" selected>${municipio.nome}</option>`);
                } else {
                    selectMunicipio.append(`<option value="${municipio.nome}">${municipio.nome}</option>`);
                }
            });
        });
    }

    $('#btn_imprimir').click(function() {
        window.open("?secao=ficha-orgao&id=<?php echo $busca['dados'][0]['orgao_id']  ?>", '_blank');
    });


    $('#estado').change(function() {
        const estadoId = $(this).val();
        if (estadoId) {
            $('#municipio').empty().append('<option value="">Aguarde...</option>');
            carregarMunicipios(estadoId);
        } else {
            $('#municipio').empty().append('<option value="" selected>Município</option>');
        }
    });


    $('#tipo').change(function() {
        if ($('#tipo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
                window.location.href = "?secao=orgaos-tipos";
            } else {
                $('#tipo').val(1000).change();
            }
        }
    });

    $('#btn_novo_tipo').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
            window.location.href = "?secao=orgaos-tipos";
        } else {
            return false;
        }
    });
</script>
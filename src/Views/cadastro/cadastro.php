<?php

use GabineteDigital\Controllers\ClienteController;

require 'vendor/autoload.php';

$clienteController = new ClienteController();

?>

<link href="public/css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">

        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-1">Gabinete Digital</h2>
        <h6 class="host mb-3">Novo Gabinete</h6>

        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
            $cliente = [
                'cliente_nome' => htmlspecialchars($_POST['cliente_nome'], ENT_QUOTES, 'UTF-8'),
                'cliente_email' => htmlspecialchars($_POST['cliente_email'], ENT_QUOTES, 'UTF-8'),
                'cliente_telefone' => preg_replace('/[^0-9]/', '', $_POST['cliente_telefone']),
                'cliente_cpf' => preg_replace('/[^0-9]/', '', $_POST['cliente_cpf_cnpj']),
                'cliente_endereco' => htmlspecialchars($_POST['cliente_endereco'], ENT_QUOTES, 'UTF-8'),
                'cliente_cep' => preg_replace('/[^0-9]/', '', $_POST['cliente_cep']),
                'cliente_assinaturas' => preg_replace('/[^0-9]/', '', $_POST['cliente_assinaturas']),
                'cliente_ativo' => 1,
                'cliente_deputado_nome' => htmlspecialchars($_POST['cliente_deputado_nome'], ENT_QUOTES, 'UTF-8'),
                'cliente_deputado_tipo' => htmlspecialchars($_POST['cliente_deputado_tipo'], ENT_QUOTES, 'UTF-8'),
                'cliente_deputado_estado' => htmlspecialchars($_POST['cliente_deputado_estado'], ENT_QUOTES, 'UTF-8'),
            ];

            $result = $clienteController->criarCliente($cliente);

            if ($result['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">' . $result['message'] . '</div>';
            } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="0" role="alert">' . $result['message'] . '</div>';
            } else if ($result['status'] == 'error') {
                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="0" role="alert">' . $result['message'] . ' | Código do erro: ' . $result['id_erro'] . '</div>';
            }
        }

        ?>
        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="cliente_nome" placeholder="Nome do gestor" required>
            </div>
            <div class="col-md-12 col-12">
                <input type="email" class="form-control form-control-sm" name="cliente_email" placeholder="Email" required>
            </div>
            <div class="col-md-6 col-12">
                <input type="text" class="form-control form-control-sm" name="cliente_endereco" placeholder="Endereço" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="cliente_cep" placeholder="CEP" data-mask="00000-000" required>
            </div>
            <div class="col-md-12 col-6">
                <input type="text" class="form-control form-control-sm" name="cliente_cpf_cnpj" placeholder="CPF" data-mask="000.000.000-00" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="cliente_telefone" placeholder="Telefone (com DDD)" data-mask="(00) 00000-0000" maxlength="15" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="cliente_assinaturas" placeholder="Licenças" data-mask="00">
            </div>
            <div class="col-md-6 col-12">
                <select class="form-select form-select-sm form_dep" name="cliente_deputado_tipo" required>
                    <option selected>Tipo do Gabinete</option>
                    <option value="Deputado Estadual">Deputado(a) Estadual</option>
                    <option value="Deputado Federal">Deputado(a) Federal</option>
                    <option value="Governador">Governador(a)</option>
                    <option value="Prefeito">Prefeito(a)</option>
                    <option value="Senador">Senador(a)</option>
                    <option value="Vereador">Vereador(a)</option>
                </select>
            </div>
           

            <div class="col-md-6 col-12">
                <select class="form-select form-select-sm form_dep" name="cliente_deputado_estado" required>
                    <option selected>Escolha o estado</option>
                    <option value="AC">Acre</option>
                    <option value="AL">Alagoas</option>
                    <option value="AM">Amazonas</option>
                    <option value="AP">Amapá</option>
                    <option value="BA">Bahia</option>
                    <option value="CE">Ceará</option>
                    <option value="DF">Distrito Federal</option>
                    <option value="ES">Espírito Santo</option>
                    <option value="GO">Goiás</option>
                    <option value="MA">Maranhão</option>
                    <option value="MT">Mato Grosso</option>
                    <option value="MS">Mato Grosso do Sul</option>
                    <option value="MG">Minas Gerais</option>
                    <option value="PA">Pará</option>
                    <option value="PB">Paraíba</option>
                    <option value="PE">Pernambuco</option>
                    <option value="PI">Piauí</option>
                    <option value="PR">Paraná</option>
                    <option value="RJ">Rio de Janeiro</option>
                    <option value="RN">Rio Grande do Norte</option>
                    <option value="RO">Rondônia</option>
                    <option value="RR">Roraima</option>
                    <option value="RS">Rio Grande do Sul</option>
                    <option value="SC">Santa Catarina</option>
                    <option value="SE">Sergipe</option>
                    <option value="SP">São Paulo</option>
                    <option value="TO">Tocantins</option>
                </select>
            </div>
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="cliente_deputado_nome" placeholder="Nome da urna" required>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_salvar" class="btn btn-primary">Salvar</button>
                <a type="button" href="?secao=login" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
        <p class="mt-3 copyright">2024 | JS Digital System</p>
    </div>
</div>
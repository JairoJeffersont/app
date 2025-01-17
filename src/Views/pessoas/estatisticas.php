<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\ClienteController;
use GabineteDigital\Controllers\PessoaController;
use GabineteDigital\Controllers\PessoaTipoController;
use GabineteDigital\Controllers\PessoaProfissaoController;


$pessoaController = new PessoaController();
$clienteController = new ClienteController;
$pessoaTipoController = new PessoaTipoController();
$pessoaProfissaoController = new PessoaProfissaoController();


$buscaCliente = $clienteController->buscarCliente('cliente_id', $_SESSION['usuario_cliente']);
$estadoDep = ($buscaCliente['status'] == 'success') ? $buscaCliente['dados'][0]['cliente_deputado_estado'] : '';

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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-graph-up"></i> Estatísticas</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível ver informações sobre as pessoas de interesse do mandato.</p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-2">
                    <h6 class="card-title mb-2">Gênero</h6>
                    <?php
                    $sexos = [
                        'Masculino' => 'bi-gender-male',
                        'Feminino' => 'bi-gender-female',
                        'Sexo não informado' => 'bi-gender-trans'
                    ];

                    foreach ($sexos as $sexo => $icone) {
                        $resultado = $pessoaController->buscarPessoa('pessoa_sexo', $sexo);

                        if ($resultado['status'] == 'success') {
                            $quantidade = count($resultado['dados']);
                            echo "<p class='card-text mb-0'><i class='$icone'></i> $sexo: $quantidade</p>";
                        } else if ($resultado['status'] == 'not_found') {
                            echo "<p class='card-text mb-0'><i class='$icone'></i> $sexo: 0</p>";
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-2">
                    <h6 class="card-title mb-2">Tipo</h6>
                    <?php
                    $buscaTipos = $pessoaTipoController->listarPessoasTipos($_SESSION['usuario_cliente']);
                    if ($buscaTipos['status'] == 'success') {
                        foreach ($buscaTipos['dados'] as $tipo) {
                            $resultado = $pessoaController->buscarPessoa('pessoa_tipo', $tipo['pessoa_tipo_id']);
                            if ($resultado['status'] == 'success') {
                                $quantidade = count($resultado['dados']);
                                echo "<p class='card-text mb-0'><i class='bi bi-person'></i> {$tipo['pessoa_tipo_nome']}: $quantidade</p>";
                            } else if ($resultado['status'] == 'not_found') {
                                echo "<p class='card-text mb-0'><i class='bi bi-person'></i> {$tipo['pessoa_tipo_nome']}: 0</p>";
                            }
                        }
                    } else {
                        echo "<p class='text-danger mb-0'>{$buscaTipos['message']}</p>";
                    }
                    ?>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body p-2">
                    <h6 class="card-title mb-2">Profissão</h6>
                    <?php
                    $buscaProfissoes = $pessoaProfissaoController->listarProfissoes($_SESSION['usuario_cliente']);
                    if ($buscaProfissoes['status'] == 'success') {
                        foreach ($buscaProfissoes['dados'] as $profissao) {
                            $resultado = $pessoaController->buscarPessoa('pessoa_profissao', $profissao['pessoas_profissoes_id']);
                            if ($resultado['status'] == 'success') {
                                $quantidade = count($resultado['dados']);
                                echo "<p class='card-text mb-0'><i class='bi bi-person-badge'></i> {$profissao['pessoas_profissoes_nome']}: $quantidade</p>";
                            } else if ($resultado['status'] == 'not_found') {
                                echo "<p class='card-text mb-0'><i class='bi bi-person-badge'></i> {$profissao['pessoas_profissoes_nome']}: 0</p>";
                            }
                        }
                    } else {
                        echo "<p class='text-danger mb-0'>Erro ao buscar profissões: {$buscaProfissoes['message']}</p>";
                    }
                    ?>
                </div>
            </div>
            <?php
            $estados = [
                'AC' => 'Acre',
                'AL' => 'Alagoas',
                'AP' => 'Amapá',
                'AM' => 'Amazonas',
                'BA' => 'Bahia',
                'CE' => 'Ceará',
                'DF' => 'Distrito Federal',
                'ES' => 'Espírito Santo',
                'GO' => 'Goiás',
                'MA' => 'Maranhão',
                'MT' => 'Mato Grosso',
                'MS' => 'Mato Grosso do Sul',
                'MG' => 'Minas Gerais',
                'PA' => 'Pará',
                'PB' => 'Paraíba',
                'PR' => 'Paraná',
                'PE' => 'Pernambuco',
                'PI' => 'Piauí',
                'RJ' => 'Rio de Janeiro',
                'RN' => 'Rio Grande do Norte',
                'RS' => 'Rio Grande do Sul',
                'RO' => 'Rondônia',
                'RR' => 'Roraima',
                'SC' => 'Santa Catarina',
                'SP' => 'São Paulo',
                'SE' => 'Sergipe',
                'TO' => 'Tocantins'
            ];
            ?>
            <div class="card mb-2">
                <div class="card-body p-2">
                    <h6 class="card-title mb-2">Pessoas por Estado</h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nenhumEncontrado = false;
                            foreach ($estados as $sigla => $nome) {
                                $resultado = $pessoaController->buscarPessoa('pessoa_estado', $sigla);
                                if ($resultado['status'] == 'success') {
                                    $quantidade = count($resultado['dados']);
                                    echo "
                                        <tr>
                                            <td><i class='bi bi-geo-alt'></i> $sigla - $nome</td>                                            
                                            <td>$quantidade</td>
                                        </tr>";
                                } else if ($resultado['status'] == 'not_found' && !$nenhumEncontrado) {
                                    echo "<tr colspan=2><td>Nenhum usuário encontrado<td></tr>";
                                    $nenhumEncontrado = true; // Marca que já foi exibido
                                }
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
<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\PessoaController;

$pessoaController = new PessoaController();

$buscaAnivesariantes = $pessoaController->buscarPessoa('pessoa_aniversario', date('d/m'));

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

            <div class="row g-3">
                <!-- Card com logo -->
                <div class="col-12 col-lg-4">
                    <div class="card  shadow-sm rounded" style="min-height: 260px;">
                        <div class="card-body text-center">
                            <img class="img-fluid mb-3" src="public/img/logo.png" width="150" />
                            <h4 class="card-title text-primary">Gabinete Digital</h4>
                            <p class="card-text text-muted">Sistema de gestão política</p>
                        </div>
                    </div>
                </div>

                <!-- Informações do cliente -->
                <div class="col-12 col-lg-4">
                    <div class="card  shadow-sm rounded" style="min-height: 260px;">
                        <div class="card-body px-4 py-3">
                            <h4 class="card-title text-success">Informações</h4>
                            <p class="card-text mb-2"><strong>Gestor do sistema:</strong> <?php echo $_SESSION['cliente_nome'] ?></p>
                            <p class="card-text mb-2"><strong>Tipo do gabinete:</strong> <?php echo $_SESSION['cliente_deputado_tipo'] ?></p>
                            <p class="card-text mb-2"><strong>Político do gabinete:</strong> <?php echo $_SESSION['cliente_deputado_nome'] . '/' . $_SESSION['cliente_deputado_estado'] ?></p>
                            <p class="card-text mb-2"><strong>Quantidade de licenças:</strong> <?php echo $_SESSION['cliente_assinaturas'] ?></p>
                            <p class="card-text mb-2"><i class="bi bi-check-circle-fill text-success"></i> <strong>Assinatura ativa</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Card em branco -->
                <div class="col-12 col-lg-4">
                    <div class="card  shadow-sm rounded" style="min-height: 260px;">
                        <div class="card-body p-3">
                            <h4 class="card-title text-warning">Licenciamento e Termos de Uso</h4>
                            <p class="card-text mb-2"><strong>Licença de Código:</strong> O código-fonte deste software é disponibilizado sob uma licença <strong>Open Source</strong>, permitindo o uso, modificação e distribuição do código, conforme os termos estabelecidos pela licença adotada.</p>
                            <p class="card-text mb-2"><strong>Modelo de Comercialização:</strong> O acesso ao software é fornecido por meio de uma assinatura paga, com opções de renovação mensal ou anual, conforme plano escolhido pelo cliente.</p>
                            <p class="card-text mb-2"><strong>Propriedade do Código-Fonte:</strong> O código-fonte do software permanece de propriedade exclusiva da desenvolvedora, sendo mantido como <strong>privado</strong> e não acessível para distribuição ou modificação fora dos termos acordados.</p>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
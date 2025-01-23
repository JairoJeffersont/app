<?php
ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\OrgaoController;
use GabineteDigital\Controllers\PessoaTipoController;
use GabineteDigital\Controllers\PessoaProfissaoController;
use GabineteDigital\Controllers\PessoaController;


$orgaoController = new OrgaoController();
$pessoaController = new PessoaController();
$pessoaTipoController = new PessoaTipoController();
$pessoaProfissaoController = new PessoaProfissaoController();

$pessoaGet = $_GET['id'];

$buscaPessoa = $pessoaController->buscarPessoa('pessoa_id', $pessoaGet);

if ($buscaPessoa['status'] == 'not_found' || is_integer($pessoaGet) || $buscaPessoa['status'] == 'error') {
    header('Location: ?secao=pessoas');
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
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=pessoas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-people-fill"></i> Ficha da pessoa</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é exibida a ficha da pessoa escolhida, garantindo a visualização organizada dessas informações no sistema.</p>
                </div>
            </div>

            <div class="row gx-2">
                <div class="col-md-4">
                    
                    <div class="card profile-card text-white text-center p-4 mb-2" style="background-image: url('public/img/bg_profile.png'); object-fit: cover;">
                        <?php
                        $foto = isset($buscaPessoa['dados'][0]['pessoa_foto']) && file_exists($buscaPessoa['dados'][0]['pessoa_foto']) ? $buscaPessoa['dados'][0]['pessoa_foto'] : 'public/img/not_found.jpg';
                        ?>
                        <img src="<?php echo $foto ?>" alt="User Photo" class="mx-auto rounded-circle shadow">
                        <h4><?php
                            $nomeCompleto = $buscaPessoa['dados'][0]['pessoa_nome'];
                            $nomes = explode(' ', $nomeCompleto);
                            echo $nomes[0] . ' ' . $nomes[count($nomes) - 1];
                            ?></h4>
                        <p class="mb-0"><?php echo $buscaPessoa['dados'][0]['orgao_nome'] ?></p>
                        <p><?php echo $buscaPessoa['dados'][0]['pessoa_tipo_nome'] ?></p>
                        <div class="d-flex justify-content-center gap-2 mt-1">
                            <a href="?secao=pessoa&id=<?php echo $pessoaGet ?>" class="btn btn-primary btn-sm px-2 py-1 shadow-sm" style="font-size: 0.850em;">Editar</a>
                            <button class="btn btn-success btn-sm px-2 py-1 shadow-sm" id="btn_imprimir" style="font-size: 0.850em;">Imprimir</button>
                            
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="p-3 bg-white rounded mb-2">
                        <h6 class="card-title font-italic mb-2">Informações Pessoais</h6>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Nome: </strong><?php echo $buscaPessoa['dados'][0]['pessoa_nome'] ?></li>
                            <li><strong>Aniversario:</strong> <?php echo $buscaPessoa['dados'][0]['pessoa_aniversario'] ?></li>
                            <li><strong>Profissão:</strong> <?php echo $buscaPessoa['dados'][0]['pessoas_profissoes_nome'] ?></li>
                            <li><strong>Cargo:</strong> <?php echo $buscaPessoa['dados'][0]['pessoa_cargo'] ?></li>
                        </ul>
                    </div>
                    <!-- Terceira coluna dentro da mesma linha -->
                    <div class="p-3 bg-white rounded mb-2">
                        <h6 class="card-title font-italic mb-2">Informações de contato</h6>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Email:</strong> <?php echo $buscaPessoa['dados'][0]['pessoa_email'] ?></li>
                            <li><strong>Telefone:</strong> <?php echo $buscaPessoa['dados'][0]['pessoa_telefone'] ?></li>
                            <li><strong>Endereço:</strong> <?php echo $buscaPessoa['dados'][0]['pessoa_endereco'] ?> - <?php echo $buscaPessoa['dados'][0]['pessoa_bairro'] ?></li>
                            <li><strong>CEP:</strong> <?php echo $buscaPessoa['dados'][0]['pessoa_cep'] ?></li>
                            <li><strong>Município/UF:</strong> <?php echo $buscaPessoa['dados'][0]['pessoa_municipio'] ?>/<?php echo $buscaPessoa['dados'][0]['pessoa_estado'] ?></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card profile-card text-center p-2 mb-2">
                        <?php echo $buscaPessoa['dados'][0]['pessoa_informacoes'] == null ? 'Sem informações adicionais' : $buscaPessoa['dados'][0]['pessoa_informacoes'] ?>
                    </div>
                </div>
            </div>





        </div>
    </div>
</div>
<script>
    $('#btn_editar').click(function() {
        window.location.href = "?secao=pessoa&id=<?php echo $pessoaGet; ?>";
    });
</script>
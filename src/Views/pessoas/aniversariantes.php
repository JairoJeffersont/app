<?php

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\PessoaController;

$pessoaController = new PessoaController();

$mes = $_GET['mes'] ?? date('m');


$estadoDep = $_SESSION['cliente_deputado_estado'];

$estado = (isset($_GET['estado']) && $_GET['estado'] !== 'null') ? $_SESSION['cliente_deputado_estado'] : null;

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
                <div class="card-header bg-primary text-white px-2 py-1 card-background">
                    <i class="bi bi-people-fill"></i> Aniversariantes do Mês
                </div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível visualizar e organizar os aniversariantes do mês, garantindo a correta gestão e acompanhamento dessas informações no sistema.</p>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-2 col-6">
                                    <input type="hidden" name="secao" value="aniversariantes" />
                                    <select class="form-select form-select-sm" name="mes" required>
                                        <option value="">Selecione um mês</option>
                                        <?php

                                        $meses = [
                                            1 => 'Janeiro',
                                            2 => 'Fevereiro',
                                            3 => 'Março',
                                            4 => 'Abril',
                                            5 => 'Maio',
                                            6 => 'Junho',
                                            7 => 'Julho',
                                            8 => 'Agosto',
                                            9 => 'Setembro',
                                            10 => 'Outubro',
                                            11 => 'Novembro',
                                            12 => 'Dezembro'
                                        ];

                                        foreach ($meses as $numero => $nome) {
                                            if ($mes == $numero) {
                                                echo "<option value=\"$numero\" selected>$nome</option>";
                                            } else {
                                                echo "<option value=\"$numero\">$nome</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="estado" required>
                                        <option value="null" <?php echo $estado === null ? 'selected' : ''; ?>>Todos os estados</option>
                                        <option value="<?php echo $estadoDep ?>" <?php echo $estado === $estadoDep ? 'selected' : ''; ?>>Somente <?php echo $estadoDep ?></option>
                                    </select>
                                </div>

                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-body p-2">
                    <div class="list-group">

                        <?php
                        $buscaMes = $pessoaController->buscarAniversarianteMes($mes, $estado, $_SESSION['usuario_cliente']);
                        if ($buscaMes['status'] == 'success') {
                            $grupos = [];
                            foreach ($buscaMes['dados'] as $aniversariante) {
                                $foto = isset($aniversariante['pessoa_foto']) && file_exists($aniversariante['pessoa_foto']) ? $aniversariante['pessoa_foto'] : 'public/img/not_found.jpg';
                                $dia = explode('/', $aniversariante['pessoa_aniversario'])[0]; // Pega o dia
                                $grupos[$dia][] = [
                                    'nome' => $aniversariante['pessoa_nome'],
                                    'id' => $aniversariante['pessoa_id'],
                                    'email' => $aniversariante['pessoa_email'],
                                    'foto' => $foto,
                                ];
                            }
                        ?>

                            <div class="accordion" id="accordionAniversariantes">
                                <?php foreach ($grupos as $dia => $aniversariantesDoDia): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?= $dia ?>">
                                            <button class="accordion-button collapsed" style="font-size: 0.5em" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $dia ?>" aria-expanded="false" aria-controls="collapse<?= $dia ?>">
                                                Dia <?= ($dia == date('d') && $mes == date('m') ? $dia . ' | <b>&nbsp;Hoje</b>' : $dia) ?>
                                            </button>
                                        </h2>
                                        <div id="collapse<?= $dia ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $dia ?>" data-bs-parent="#accordionAniversariantes">
                                            <div class="accordion-body p-3">
                                                <?php foreach ($aniversariantesDoDia as $aniversariante): ?>
                                                    <a href="?secao=ficha-pessoa&id=<?= $aniversariante['id'] ?>" class="shadow-sm list-group-item list-group-item-action d-flex align-items-center">
                                                        <img src="<?= $aniversariante['foto'] ?>" alt="Foto de <?= $aniversariante['nome'] ?>" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <div>
                                                            <h5 class="mb-1" style="font-size: 1.2em; font-weight: 600"><?= $aniversariante['nome'] ?></h5>
                                                            <p class="mb-1" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-all;"><?= $aniversariante['email'] ?></p>
                                                        </div>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php
                        } else if ($buscaMes['status'] == 'not_found') {
                            echo '<div class="list-group-item list-group-item-action d-flex align-items-center">                                       
                                        <div>
                                            <h5 class="mb-0" style="font-size: 1em;">Nenhum aniversariante neste mês</h5>
                                        </div>
                                </div>';
                        } else if ($buscaMes['status'] == 'error') {
                            echo '<div class="list-group-item list-group-item-action d-flex align-items-center">                                       
                                        <div>
                                            <h5 class="mb-0" style="font-size: 1em;">' . $buscaMes['message'] . ' | Código do erro: ' . $buscaMes['error_id'] . '</h5>
                                        </div>
                                </div>';
                        }
                        ?>




                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
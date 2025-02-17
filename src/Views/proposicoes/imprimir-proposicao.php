<?php

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$proposicaoIdGet = $_GET['id'];

$buscaProposicao = $proposicaoController->buscarDetalhe($proposicaoIdGet);

if ($buscaProposicao['status'] == 'error' || empty($buscaProposicao['dados'])) {
    header('location: ?secao=proposicoes');
}
$buscaNota = $notaController->buscarNotaTecnica('nota_proposicao', $proposicaoIdGet);
$buscaAutores = $proposicaoController->buscarAutores($proposicaoIdGet);

?>


<style>
    body {
        background-image: none !important;
        background-color: white !important;
    }

    @media print {

        @page {
            margin: 0;
            margin-top: 15mm;
            margin-bottom: 15mm;
            size: A4 portrait;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }



        header,
        footer {
            display: none !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    }
</style>

<script>
    window.onload = function() {
        window.print();
        window.onafterprint = function() {
            window.close();
        };
    };
</script>

<div class="container-fluid p-2">
    <div class="row ">
        <div class="col-12">
            <div class="card" style="background: none; border: none;">
                <div class="card-body text-center" style="background: none;">
                    <img src="public/img/brasaooficialcolorido.png" class="img-fluid mb-2" style="width: 150px;" />
                    <h5 class="card-title mb-2">Gabinete do <?php echo $_SESSION['cliente_deputado_tipo'] ?> <?php echo $_SESSION['cliente_deputado_nome'] ?></h5>
                    <p class="card-text" style="font-size: 1.4em;">Ficha da proposição </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3 mb-2 d-flex justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h5 class="card-title mb-4"><?php echo $buscaProposicao['dados']['siglaTipo'] . ' ' . $buscaProposicao['dados']['numero'] . '/' . $buscaProposicao['dados']['ano']; ?></h5>
                    <?php

                    if ($buscaNota['status'] == 'success' && !empty($buscaNota['dados'])) {
                        echo '<p class="card-text mb-3">' . $buscaNota['dados'][0]['nota_proposicao_apelido'] . '</p>';
                        echo '<p class="card-text mb-3"><b>Resumo</b></p>';
                        echo '<p class="card-text mb-3">' . $buscaNota['dados'][0]['nota_proposicao_resumo'] . '</p>';
                        echo '<p class="card-text mb-3"><b>Ementa</b></p>';
                        echo '<p class="card-text mb-0"><em>' . $buscaProposicao['dados']['ementa'] . '</em></p>';
                    } else if ($buscaNota['status'] == 'not_found') {
                        echo '<p class="card-text mb-3"><b>Ementa</b></p>';
                        echo '<p class="card-text mb-0"><em>' . $buscaProposicao['dados']['ementa'] . '</em></p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row d-flex mb-2 justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Informações gerais</h6>
                    <hr>

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
    </div>
    <div class="row d-flex mb-2 justify-content-center">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Nota técnica</h6>
                    <hr>
                    <?php

                    if ($buscaNota['status'] == 'success' && !empty($buscaNota['dados'])) {
                        echo $buscaNota['dados'][0]['nota_texto'];
                    } else if ($buscaNota['status'] == 'not_found') {
                        echo '<p class="card-text mb-3">Não existe uma nota técnica para essa proposição</p>';
                    }

                    ?>

                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex mb-2 justify-content-center ">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Últimas Tramitações</h6>
                    <hr>
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 ">
                            <thead>
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Despacho</th>
                                    <th scope="col">Órgão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaTramitacoes = $proposicaoController->buscarTramitacoes($proposicaoIdGet);

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

                </div>
            </div>
        </div>
    </div>





</div>
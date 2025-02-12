<?php


ob_start();



use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

$anoGet = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$autorGet = $_SESSION['cliente_deputado_nome'];
$tipoget = isset($_GET['tipo']) ? $_GET['tipo'] : 'pl';
$ordenarPorGet = isset($_GET['ordenarPor']) ? $_GET['ordenarPor'] : 'proposicao_numero';
$ordemGet = isset($_GET['ordem']) ? $_GET['ordem'] : 'desc';
$itensGet = isset($_GET['itens']) ? (int)$_GET['itens'] : 10;
$paginaGet = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$arquivadoGet = isset($_GET['arquivado']) ? (int)$_GET['arquivado'] : 0;
$autoriaGet = isset($_GET['autoria']) ? (int)$_GET['autoria'] : 1;

?>


<div class="card mb-2 card-description">
    <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-newspaper"></i> Proposições | <?php echo $_SESSION['cliente_deputado_tipo'] ?></div>
    <div class="card-body p-2">
        <p class="card-text mb-2">Nesta seção, você pode pesquisar pelas proposições do deputado, facilitando o acesso às informações relevantes de forma rápida e organizada.</p>
        <p class="card-text mb-0">As informações são fornecidas pela Câmara dos Deputados.</p>
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
                    </select>
                </div>
                <div class="col-md-1 col-6">
                    <select class="form-select form-select-sm" name="autoria" required>
                        <option value="1" <?php echo $autoriaGet === 1 ? 'selected' : ''; ?>>Autoria única</option>
                        <option value="0" <?php echo $autoriaGet === 0 ? 'selected' : ''; ?>>Autoria compartilhada</option>
                    </select>
                </div>
                <div class="col-md-2 col-6">
                    <select class="form-select form-select-sm" name="arquivado" required>
                        <option value="1" <?php echo $arquivadoGet === 1 ? 'selected' : ''; ?>>Arquivado</option>
                        <option value="0" <?php echo $arquivadoGet === 0 ? 'selected' : ''; ?>>Tramitando</option>
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
                <div class="col-md-2 col-6">
                    <select class="form-select form-select-sm" name="ordem" required>
                        <option value="asc" <?php echo $ordemGet == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                        <option value="desc" <?php echo $ordemGet == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                    </select>
                </div>
                <div class="col-md-1 col-2">
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-2">
    <div class="card-body p-2">
        <div class="table-responsive mb-0">
            <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Ementa</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php 
                        $buscaProposicao = $proposicaoController->buscarProposicoesGabinete($autorGet, $anoGet, $tipoget, $itensGet, $paginaGet, $ordemGet, $ordenarPorGet, $autoriaGet, $arquivadoGet);
                        if($buscaProposicao['status'] == 'success'){
                            foreach($buscaProposicao['dados'] as $proposicao){
                                echo '<tr>';
                                echo '<td style="white-space: nowrap;">'.$proposicao['proposicao_titulo'].'</td>';
                                echo '<td>'.$proposicao['proposicao_ementa'].'</td>';
                                echo '</tr>';
                            }
                        }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
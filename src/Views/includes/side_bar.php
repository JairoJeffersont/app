<div class="border-end bg-white no-print" id="sidebar-wrapper">
    <div class="sidebar-heading border-bottom bg-light" style="font-size: 1.2em;"><img src="public/img/logo.png" width="24" />&nbsp;&nbsp; Gabinete Digital</div>
    <div class="list-group list-group-flush">
        <?php

        if ($_SESSION['cliente_deputado_tipo'] == 'Deputado Federal' || $_SESSION['cliente_deputado_tipo'] == 'Senador' || $_SESSION['cliente_deputado_tipo'] == 'Vereador'  || $_SESSION['cliente_deputado_tipo'] == 'Deputado Estadual') {
            echo ' <p style="margin-left: 10px; margin-top:20px;font-weight: bolder;" class="text-muted"><i class="bi bi-list"></i> Assessoria Legislativa</p>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" id="link" href="?secao=proposicoes"><i class="bi bi-file-earmark-text"></i> Proposições do gabinete</a>';
        } 
        ?>

        <!--<a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=reunioes"><i class="bi bi-calendar3"></i> Agenda da Câmara</a>-->

        <p style="margin-left: 10px; margin-top:20px;font-weight: bolder;" class="text-muted"><i class="bi bi-list"></i> Gestão de pessoas</p>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=orgaos"><i class="bi bi-building"></i> Órgãos e instituições</a>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=pessoas"><i class="bi bi-people-fill"></i> Pessoas</a>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=estatisticas&estado=<?php echo $_SESSION['cliente_deputado_estado'] ?>"><i class="bi bi-arrow-return-right"></i> Estatísticas</a>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=aniversariantes"><i class="bi bi-cake"></i> Aniversariantes</a>

        <p style="margin-left: 10px; margin-top:20px;font-weight: bolder;" class="text-muted"><i class="bi bi-list"></i> Agendas</p>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=agendas"><i class="bi bi-calendar3"></i> Agenda de compromissos</a>

        <p style="margin-left: 10px; margin-top:20px;font-weight: bolder;" class="text-muted"><i class="bi bi-list"></i> Gestão do gabinete</p>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=documentos"><i class="bi bi-file-earmark-text"></i> Documentos</a>

        <p style="margin-left: 10px; margin-top:20px;font-weight: bolder;" class="text-muted"><i class="bi bi-list"></i> Orçamento</p>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=emendas"><i class="bi bi-cash-stack"></i> Emendas parlamentares</a>


        <p style="margin-left: 10px; margin-top:20px;font-weight: bolder;" class="text-muted"><i class="bi bi-list"></i> Comunicação do gabinete</p>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=postagens"><i class="bi bi-instagram"></i> Postagens</a>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=clippings"><i class="bi bi-newspaper"></i> Clipping</a>
        <a class="list-group-item list-group-item-action list-group-item-light px-4" href="?secao=estatisticas-clipping"><i class="bi bi-arrow-return-right"></i> Estatísticas</a>
    </div>
</div>
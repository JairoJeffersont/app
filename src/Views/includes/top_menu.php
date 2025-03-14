<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom no-print">
    <div class="container-fluid">
        <button class="btn btn-primary" id="sidebarToggle">Menu</button>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Configurações</a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="?secao=tipos-orgaos">Tipos de órgãos</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?secao=tipos-pessoas">Tipos de pessoas</a>
                        <a class="dropdown-item" href="?secao=profissoes">Profissões</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?secao=tipos-documentos">Tipos de documentos</a>

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?secao=status-postagens">Status das postagens</a>
                        <a class="dropdown-item" href="?secao=tipos-clipping">Tipos de clipping</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?secao=status-emendas">Status de emenda</a>
                        <a class="dropdown-item" href="?secao=objetivos-emendas">Objetivos da emenda</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?secao=tipos-agendas">Tipos de agenda</a>
                        <a class="dropdown-item" href="?secao=situacoes-agendas">Situações da agenda</a>
                        <div class="dropdown-divider"></div>

                        <?php
                        if ($_SESSION['cliente_deputado_tipo'] == 'Deputado Federal' || $_SESSION['cliente_deputado_tipo'] == 'Senador' || $_SESSION['cliente_deputado_tipo'] == 'Vereador'  || $_SESSION['cliente_deputado_tipo'] == 'Deputado Estadual') {
                            echo ' <a class="dropdown-item" href="?secao=temas-proposicoes">Temas de proposições</a>
                        <a class="dropdown-item" href="?secao=proposicoes_tramitacoes">Tramitações de proposições</a>';
                        }

                        ?>




                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Gabinete</a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="?secao=usuarios"><i class="bi bi-people-fill"></i> Usuários</a>

                        <?php
                        if ($_SESSION['usuario_nivel'] == 0) {
                            echo ' <div class="dropdown-divider"></div><a class="dropdown-item" href="?secao=clientes"><i class="bi bi-person-fill-gear"></i> Clientes</a>';
                        }
                        ?>

                    </div>


                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['usuario_nome'] ?></a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="?secao=usuario&id=<?php echo $_SESSION['usuario_id'] ?>"><i class="bi bi-person-fill"></i> Perfil </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item"  href="?secao=sobre"><i class="bi bi-info-circle"></i> Sobre</a>

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" id="btn-sair" href="?secao=sair"><i class="bi bi-door-open"></i> Sair</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
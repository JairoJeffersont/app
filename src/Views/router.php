<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include './src/Views/home/home.php';

$rotas = [
    'login' => './src/Views/login/login.php',
    'sair' => './src/Views/login/sair.php',
    'recuperar-senha' => './src/Views/login/recuperar-senha.php',
    'nova-senha' => './src/Views/login/nova-senha.php',
    'cadastro' => './src/Views/cadastro/cadastro.php',
    'novo-usuario' => './src/Views/cadastro/novo-usuario.php',
    'home' => './src/Views/home/home.php',
    'fatal-error' => './src/Views/erros/fatal_error.php',
    'usuarios' => './src/Views/usuarios/usuarios.php',
    'usuario' => './src/Views/usuarios/editar-usuario.php',
    'tipos-orgaos' => './src/Views/orgaos/tipos-orgaos.php',
    'tipo-orgao' => './src/Views/orgaos/editar-tipo-orgao.php',
    'orgaos' => './src/Views/orgaos/orgaos.php',
    'orgao' => './src/Views/orgaos/editar-orgao.php',
    'tipos-pessoas' => './src/Views/pessoas/tipos-pessoas.php',
    'tipo-pessoa' => './src/Views/pessoas/editar-tipos-pessoas.php',
    'profissoes' => './src/Views/pessoas/profissoes.php',
    'profissao' => './src/Views/pessoas/editar-profissoes.php',
    'pessoas' => './src/Views/pessoas/pessoas.php',
    'aniversariantes' => './src/Views/pessoas/aniversariantes.php',
    'ficha-pessoa' => './src/Views/pessoas/ficha-pessoa.php',
    'estatisticas' => './src/Views/pessoas/estatisticas.php',
    'bairros' => './src/Views/pessoas/bairros.php',
    'pessoa' => './src/Views/pessoas/editar-pessoa.php',
    'oficios' => './src/Views/oficios/oficios.php',
    'oficio' => './src/Views/oficios/editar-oficio.php',
    'status-postagens' => './src/Views/postagens/status-postagens.php',
    'status-postagem' => './src/Views/postagens/editar-status-postagens.php',    
    'postagens' => './src/Views/postagens/postagens.php',
    'postagem' => './src/Views/postagens/editar-postagem.php',    
    'tipos-clipping' => './src/Views/clipping/tipos-clipping.php',    
    'tipo-clipping' => './src/Views/clipping/editar-tipos-clipping.php',        
    'clippings' => './src/Views/clipping/clippings.php',
    'clipping' => './src/Views/clipping/editar-clipping.php',
    'estatisticas-clipping' => './src/Views/clipping/estatisticas.php',





];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/erros/404.php';
}

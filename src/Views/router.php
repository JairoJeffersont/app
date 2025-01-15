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
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/erros/404.php';
}

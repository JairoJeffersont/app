<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include '../src/views/404.php';

$rotas = [
    'login' => './src/views/login/login.php',
    'sair' => './src/views/login/sair.php',
    'recuperar-senha' => './src/views/login/recuperar-senha.php',
    'nova-senha' => './src/views/login/nova-senha.php',
    'cadastro' => './src/views/cadastro/cadastro.php',
    'novo-usuario' => './src/views/cadastro/novo-usuario.php',

];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/views/404.php';
}

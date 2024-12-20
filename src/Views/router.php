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

];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/views/404.php';
}

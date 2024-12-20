<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include '../src/views/404.php';

$rotas = [
    'cadastro' => './src/views/cadastro/cadastro.php',
    'novo-usuario' => './src/views/cadastro/novo-usuario.php',
    
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/views/404.php';
}


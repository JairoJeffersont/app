<?php


ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

print_r(json_encode($proposicaoController->atualizar(2021, 204379, 'PL', 1, 4)));

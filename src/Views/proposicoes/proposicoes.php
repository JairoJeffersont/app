<?php


ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

print_r(json_encode($proposicaoController->atualizar(2023, 204379, 'PL', 10, 1)));

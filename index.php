<?php

use GabineteDigital\Controllers\LoginController;

require 'vendor/autoload.php';

$a = new LoginController();

print_r($a->novaSenha('6764779c26f6f', 'intell01'));

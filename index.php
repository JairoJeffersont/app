<?php

use GabineteDigital\Controllers\LoginController;

require 'vendor/autoload.php';

$a = new LoginController();

print_r($a->recuperarSenha('jairojeffersont@gmail.com'));

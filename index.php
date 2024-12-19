<?php

use GabineteDigital\Controllers\LoginController;

require 'vendor/autoload.php';

$a = new LoginController();

print_r($a->Logar('teste@teste.com', 'intell01'));

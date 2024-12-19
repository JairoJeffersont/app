<?php

use GabineteDigital\Controllers\LoginController;

require 'vendor/autoload.php';

$a = new LoginController();

print_r($a->novaSenha('6764724136af6', 'intell01'));

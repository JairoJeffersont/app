<?php

session_start();

if (!isset($_SESSION['usuario_nome']) || (isset($_SESSION['expiracao']) && time() > $_SESSION['expiracao'])) {
    session_unset();
    session_destroy();
    header('Location: ?secao=login');
    exit();
}

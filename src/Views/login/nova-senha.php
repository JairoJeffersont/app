<link href="public/css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">
        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-2">Gabinete Digital</h2>
        <h6 class="host mb-3">Digite a nova senha</h6>

        <?php

        $token = $_GET['token'] ? $_GET['token'] : null;

        if ($token == null) {
            header('Location: ?secao=login');
        }

        use GabineteDigital\Controllers\LoginController;
        use GabineteDigital\Controllers\UsuarioController;

        require_once './vendor/autoload.php';

        $loginController = new LoginController();
        $usuarioController = new UsuarioController();

        $buscaToken = $usuarioController->buscarUsuario('usuario_token', $token);

        if ($buscaToken['status'] != 'success') {
            header('Location: ?secao=login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

            $senha = htmlspecialchars($_POST['usuario_senha'], ENT_QUOTES, 'UTF-8');

            $resultado = $loginController->novaSenha($token, $senha);

            if ($resultado['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2  rounded-5 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            } else if ($resultado['status'] == 'not_found' || $resultado['status'] == 'deactivated') {
                echo '<div class="alert alert-info px-2 py-1 mb-2  rounded-5 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            } else if ($resultado['status'] == 'wrong_password' || $resultado['status'] == 'error' || $resultado['status'] == 'deactived') {
                echo '<div class="alert alert-danger px-2 rounded-5 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            }
        }

        ?>

        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-12 col-12">
                <input type="password" class="form-control form-control-sm" name="usuario_senha" placeholder="Nova senha" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_salvar" class="btn btn-primary">Salvar</button>
                <a type="button" href="?secao=login" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
        <p class="mt-3 copyright"><?php echo date('Y') ?> | JS Digital System</p>
    </div>
</div>
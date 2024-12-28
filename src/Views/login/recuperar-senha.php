<link href="public/css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">
        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-2">Gabinete Digital</h2>
        <h6 class="host mb-3">Digite o email cadastrado</h6>

        <?php
        
        use GabineteDigital\Controllers\LoginController;

        require_once './vendor/autoload.php';

        $loginController = new LoginController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_recuperar'])) {

            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

            $resultado = $loginController->recuperarSenha($email);

            if ($resultado['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2  rounded-5 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            } else if ($resultado['status'] == 'not_found' || $resultado['status'] == 'deactivated') {
                echo '<div class="alert alert-info px-2 py-1 mb-2  rounded-5 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            } else if ($resultado['status'] == 'wrong_password' || $resultado['status'] == 'error' || $resultado['status'] == 'deactived') {
                echo '<div class="alert alert-danger px-2 rounded-5 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
            }
        }

        ?>

        <form id="form_login" method="post" enctype="application/x-www-form-urlencoded" class="form-group">
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" value="jairojeffersont@gmail.com" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_recuperar" class="btn btn-primary">Enviar</button>
                <a type="button" href="?secao=login" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
        <p class="mt-3 copyright"><?php echo date('Y') ?> | JS Digital System</p>
    </div>
</div>
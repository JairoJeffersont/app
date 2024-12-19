<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\EmailSender;
use GabineteDigital\Models\UsuarioModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use PDOException;

/**
 * LoginController
 * 
 * Esta classe gerencia o processo de autenticação de usuários.
 * 
 * @package GabineteDigital\Controllers
 */
class LoginController {

    /**
     * @var UsuarioModel
     * Instância do controlador de usuários.
     */
    private $usuarioModel;

    /**
     * @var array
     * Configurações do aplicativo.
     */
    private $config;

    /**
     * @var Logger
     * Instância do logger para registro de eventos.
     */
    private $logger;

    /**
     * @var EmailSender Instância do EmailSender para enviar email de confirmação.
     */
    private $emailSender;

    /**
     * Construtor do LoginController.
     * 
     * Inicializa o controlador de usuários, configurações e o logger.
     */
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->config = require './src/Configs/config.php';
        $this->logger = new Logger('login_log');
        $this->logger->pushHandler(new StreamHandler(dirname(__DIR__, 2) . '/logs/login_log.log', Level::Error));
        $this->emailSender = new EmailSender();
    }



    public function recuperarSenha($email) {
        try {
            $busca = $this->usuarioModel->buscar('usuario_email', $email);

            if (empty($busca)) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }

            $uniqid = uniqid();

            $busca[0]['usuario_token'] = $uniqid;

            $result = $this->usuarioModel->atualizar($busca[0]['usuario_id'], $busca[0]);

            if ($result) {
                $this->emailSender->sendEmail($email, 'Gabinete Digital - Recuperação de senha', 'CORPO DO EMAIL' . $uniqid); //CRIAR CORPO DO EMAIL 
                return ['status' => 'success', 'message' => 'Email de recuperação enviado com sucesso', 'token' => $uniqid];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->log(Level::Error, $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    public function novaSenha($token, $senha) {
        
        try {
            $busca = $this->usuarioModel->buscar('usuario_token', $token);

            if (empty($busca)) {
                return ['status' => 'not_found', 'message' => 'Token inválido.'];
            }

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $busca[0]['usuario_senha'] = $senhaHash;
            $busca[0]['usuario_token'] = null;

            $result = $this->usuarioModel->atualizar($busca[0]['usuario_id'], $busca[0]);

            if ($result) {
                return ['status' => 'success', 'message' => 'Senha Alterada com sucesso'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->log(Level::Error, $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

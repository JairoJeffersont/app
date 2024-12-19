<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Controllers\UsuarioController;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

/**
 * LoginController
 * 
 * Esta classe gerencia o processo de autenticação de usuários.
 * 
 * @package GabineteDigital\Controllers
 */
class LoginController {

    /**
     * @var UsuarioController
     * Instância do controlador de usuários.
     */
    private $usuarioController;

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
     * Construtor do LoginController.
     * 
     * Inicializa o controlador de usuários, configurações e o logger.
     */
    public function __construct() {
        $this->usuarioController = new UsuarioController();
        $this->config = require './src/Configs/config.php';
        $this->logger = new Logger('login_log');
        $this->logger->pushHandler(new StreamHandler(dirname(__DIR__, 2) . '/logs/login_log.log', Level::Error));
    }

    /**
     * Método responsável por realizar o login do usuário.
     * 
     * @param string $email O email do usuário.
     * @param string $senha A senha do usuário.
     * 
     * @return array Retorna o status e mensagem da tentativa de login.
     */
    public function Logar($email, $senha) {

        /**
         * Valida o formato do email.
         */
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email',  'message' => 'Email inválido.'];
        }

        /**
         * Verifica login para o usuário master.
         */
        if ($this->config['master_user']['master_email'] === $email && $this->config['master_user']['master_pass'] === $senha) {
            session_start();

            $_SESSION = [
                'expiracao' => time() + (1 * 60 * 60),
                'usuario_id' => 1,
                'usuario_nome' => $this->config['master_user']['master_name'],
                'usuario_nivel' => 0,
                'usuario_foto' => null,
                'usuario_cliente' => '1',
                'cliente_nome' => 'CLIENTE_SISTEMA',
                'cliente_deputado_id' => 1,
                'cliente_deputado_nome' => 'DEPUTADO_SISTEMA',
                'cliente_deputado_estado' => 'BR',
                'cliente_assinaturas' => 1,
            ];

            $this->logger->log(Level::Info, sprintf('%s - %s', date('Y-m-d | H:i'), $this->config['master_user']['master_name']));

            return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
        }

        $result = $this->usuarioController->buscarUsuario('usuario_email', $email);

        /**
         * Retorna status caso o usuário não seja encontrado ou houver erro.
         */
        if ($result['status'] == 'not_found' || $result['status'] == 'error' || $result['status'] == 'bad_request') {
            return $result;
        }

        /**
         * Verifica se o usuário está ativo.
         */
        if (!$result['dados'][0]['usuario_ativo']) {
            return ['status' => 'deactivated', 'status_code' => 403, 'message' => 'Usuário desativado.'];
        }

        /**
         * Verifica a senha do usuário.
         */
        if (password_verify($senha, $result[0]['usuario_senha'])) {
            session_start();

            $_SESSION = [
                'expiracao' => time() + $this->config['app']['session_time'] * 60 * 60,
                'usuario_id' => $result[0]['usuario_id'],
                'usuario_nome' => $result[0]['usuario_nome'],
                'usuario_nivel' => $result[0]['usuario_nivel'],
                'usuario_foto' => $result[0]['usuario_foto'],
                'usuario_cliente' => $result[0]['usuario_cliente'],
                'cliente_nome' => $result[0]['cliente_nome'],
                'cliente_deputado_id' => $result[0]['cliente_deputado_id'],
                'cliente_deputado_nome' => $result[0]['cliente_deputado_nome'],
                'cliente_deputado_estado' => $result[0]['cliente_deputado_estado'],
                'cliente_assinaturas' => $result[0]['cliente_assinaturas'],
            ];

            $this->logger->log(Level::Info, sprintf('%s - %s', date('Y-m-d | H:i'), $result[0]['usuario_nome']));
            return ['status' => 'success', 'status_code' => 200, 'message' => 'Usuário verificado com sucesso.'];
        } else {
            return ['status' => 'wrong_password', 'message' => 'Senha incorreta.'];
        }
    }
}

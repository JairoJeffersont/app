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
     * @var EmailSender
     * Instância do EmailSender para enviar email de confirmação.
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

    /**
     * Loga um usuário no sistema.
     * 
     * @param string $email
     * @param string $senha
     * @return array
     */
    public function Logar($email, $senha) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email',  'message' => 'Email inválido.'];
        }

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

        try {
            $busca = $this->usuarioModel->buscar('usuario_email', $email);

            if (empty($busca)) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }

            if (!$busca[0]['usuario_ativo']) {
                return ['status' => 'deactivated', 'message' => 'Usuário desativado.'];
            }

            if (password_verify($senha, $busca[0]['usuario_senha'])) {
                session_start();

                $_SESSION = [
                    'expiracao' => time() + $this->config['app']['session_time'] * 60 * 60,
                    'usuario_id' => $busca[0]['usuario_id'],
                    'usuario_nome' => $busca[0]['usuario_nome'],
                    'usuario_nivel' => $busca[0]['usuario_nivel'],
                    'usuario_foto' => $busca[0]['usuario_foto'],
                    'usuario_cliente' => $busca[0]['usuario_cliente'],
                    'cliente_nome' => $busca[0]['cliente_nome'],
                    'cliente_deputado_id' => $busca[0]['cliente_deputado_id'],
                    'cliente_deputado_nome' => $busca[0]['cliente_deputado_nome'],
                    'cliente_deputado_estado' => $busca[0]['cliente_deputado_estado'],
                    'cliente_assinaturas' => $busca[0]['cliente_assinaturas'],
                ];

                $this->logger->log(Level::Info, sprintf('%s - %s', date('Y-m-d | H:i'), $busca[0]['usuario_nome']));
                return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
            } else {
                return ['status' => 'wrong_password', 'message' => 'Senha incorreta.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->log(Level::Error, $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    /**
     * Recupera a senha de um usuário, enviando um email de recuperação.
     * 
     * @param string $email
     * @return array
     */
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


    /**
     * Gera uma nova senha utilizando o token de recuperação.
     * 
     * @param string $token
     * @param string $senha
     * @return array
     */
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

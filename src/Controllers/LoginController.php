<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\EmailSender;
use GabineteDigital\Models\UsuarioModel;
use GabineteDigital\Middleware\Logger;

use PDOException;

/**
 * LoginController
 * 
 * Esta classe gerencia o processo de autenticação de usuários no sistema.
 * Responsável por validar informações de login, gerenciar sessões e controlar o fluxo de recuperação de senha.
 * 
 * @package GabineteDigital\Controllers
 */
class LoginController
{

    /**
     * @var UsuarioModel
     * Instância do controlador de usuários, utilizado para interagir com os dados do usuário.
     */
    private $usuarioModel;

    /**
     * @var array
     * Configurações gerais do aplicativo, como informações de usuário mestre e configurações de sessão.
     */
    private $config;

    /**
     * @var Logger
     * Instância do logger para registro de eventos, erros e informações importantes.
     */
    private $logger;

    /**
     * @var EmailSender
     * Instância do serviço responsável por enviar emails, como o de recuperação de senha.
     */
    private $emailSender;


    /**
     * Construtor do LoginController.
     * 
     * Inicializa o controlador de usuários, configurações e o logger para registro de atividades.
     */
    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->config = require './src/Configs/config.php'; // Carrega as configurações do aplicativo
        $this->logger = new Logger();
        $this->emailSender = new EmailSender(); // Inicializa o serviço de envio de emails
    }

    /**
     * Loga um usuário no sistema.
     * 
     * Valida o email e a senha do usuário, verificando se o usuário é ativo e retornando informações de sessão.
     * 
     * @param string $email
     * @param string $senha
     * @return array
     */
    public function Logar($email, $senha)
    {

        // Valida o formato do email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email',  'message' => 'Email inválido.'];
        }

        // Verifica se o login é o usuário mestre configurado
        if ($this->config['master_user']['master_email'] === $email && $this->config['master_user']['master_pass'] === $senha) {
            session_start(); // Inicia a sessão

            $_SESSION = [
                'expiracao' => time() + (1 * 60 * 60), // Define a expiração da sessão para 1 hora
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

            // Obter informações do usuário
            $userInfo = $this->logger->get_user_info();

            // Criar a mensagem do log
            $logMessage = $this->config['master_user']['master_name'] . ' | ' .
                'IP: ' . $userInfo['ip'] . ' | ' .
                'Navegador: ' . $userInfo['browser'] . ' | ' .
                'Sistema: ' . $userInfo['os'] . ' | ' .
                'User Agent: ' . $userInfo['user_agent'];

            // Gravar no log
            $this->logger->novoLog('login_log', $logMessage);

            return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
        }

        try {
            // Busca o usuário no banco de dados
            $busca = $this->usuarioModel->buscar('usuario_email', $email);

            // Verifica se o usuário foi encontrado
            if (empty($busca)) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }

            // Verifica se o usuário está ativo
            if (!$busca[0]['usuario_ativo']) {
                return ['status' => 'deactivated', 'message' => 'Usuário desativado.'];
            }

            // Verifica a senha usando a função de verificação do PHP
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

                $userInfo = $this->logger->get_user_info();

                $logMessage =  $busca[0]['cliente_deputado_nome'].' | '.$busca[0]['usuario_nome'] . ' | ' .
                    'IP: ' . $userInfo['ip'] . ' | ' .
                    'Navegador: ' . $userInfo['browser'] . ' | ' .
                    'Sistema: ' . $userInfo['os'] . ' | ' .
                    'User Agent: ' . $userInfo['user_agent'];

                // Gravar no log
                $this->logger->novoLog('login_log', $logMessage);

                return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
            } else {
                return ['status' => 'wrong_password', 'message' => 'Senha incorreta.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('login_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Recupera a senha de um usuário.
     * 
     * Envia um email com o token de recuperação de senha se o usuário for encontrado.
     * 
     * @param string $email
     * @return array
     */
    public function recuperarSenha($email)
    {
        try {
            // Busca o usuário no banco de dados usando o email
            $busca = $this->usuarioModel->buscar('usuario_email', $email);


            if (empty($busca)) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }

            // Gera um token único para recuperação de senha
            $uniqid = uniqid();
            $busca[0]['usuario_token'] = $uniqid;

            // Atualiza o token no banco de dados
            $result = $this->usuarioModel->atualizar($busca[0]['usuario_id'], $busca[0]);

            if ($result) {
                // Envia o email de recuperação com o token
                $resp = $this->emailSender->sendEmail($email, 'Gabinete Digital - Recuperação de senha', $this->config['app']['base_url'] . '?secao=nova-senha&token=' . $uniqid); // Criar corpo do email
                return ['status' => 'success', 'message' => $resp['message']];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('login_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Define uma nova senha utilizando o token recebido.
     * 
     * @param string $token
     * @param string $senha
     * @return array
     */
    public function novaSenha($token, $senha)
    {
        try {
            // Busca o usuário pelo token
            $busca = $this->usuarioModel->buscar('usuario_token', $token);

            if (empty($busca)) {
                return ['status' => 'not_found', 'message' => 'Token inválido.'];
            }

            // Cria o hash da nova senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $busca[0]['usuario_senha'] = $senhaHash;
            $busca[0]['usuario_token'] = null;

            // Atualiza a senha no banco de dados
            $result = $this->usuarioModel->atualizar($busca[0]['usuario_id'], $busca[0]);

            if ($result) {
                return ['status' => 'success', 'message' => 'Senha Alterada com sucesso'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('login_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

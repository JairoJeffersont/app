<?php

namespace GabineteDigital\Middleware;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use GabineteDigital\Middleware\Logger;

/**
 * Classe EmailSender
 * 
 * A classe `EmailSender` é responsável por enviar e-mails utilizando a biblioteca PHPMailer 
 * e registrar erros de envio em arquivos de log utilizando o Monolog.
 * 
 * @package GabineteDigital\Middleware
 */
class EmailSender {
    /** @var PHPMailer Instância do PHPMailer para envio de e-mails */
    private $mailer;

    /** @var Logger Instância do Logger para registro de erros */
    private $logger;

    /**
     * Construtor da classe EmailSender.
     * 
     * Inicializa as dependências necessárias: PHPMailer e Monolog.
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->logger = new Logger();
    }

    /**
     * Método para enviar um e-mail.
     * 
     * @param string $toEmail Endereço de e-mail do destinatário.
     * @param string $message Conteúdo da mensagem a ser enviada.
     * @return array Retorna um array associativo com o status da operação ('success' ou 'error') e uma mensagem.
     */
    public function sendEmail($toEmail, $assunto, $message) {


        try {
            $this->mailer->IsSMTP();
            $this->mailer->Host = "smtp.politikaassessoria.com.br";
            $this->mailer->SMTPAuth = true;
            $this->mailer->Port = 587;
            $this->mailer->SMTPSecure = false;
            $this->mailer->SMTPAutoTLS = true;
            $this->mailer->Username = 'contato@politikaassessoria.com.br';
            $this->mailer->Password = 'Intell@01';

            $this->mailer->addAddress($toEmail);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $assunto;
            $this->mailer->Body    = $message;

            //$this->mailer->send();
            return ['status' => 'success', 'message' => 'Email enviado com sucesso.'];
        } catch (Exception $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

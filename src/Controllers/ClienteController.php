<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\EmailSender;
use GabineteDigital\Models\ClienteModel;
use GabineteDigital\Middleware\Logger;

use PDOException;

/**
 * Classe ClienteController
 *
 * Controla as operações relacionadas a clientes, incluindo criação, atualização, listagem,
 * busca e exclusão de clientes.
 */
class ClienteController
{

    /**
     * @var ClienteModel Instância do modelo ClienteModel para interagir com os dados.
     */
    private $clienteModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * @var EmailSender Instância do EmailSender para enviar email de confirmação.
     */
    private $emailSender;

    /**
     * @var Configs Instância as configurações do sitema.
     */
    private $configs;

    /**
     * Construtor do ClienteController.
     *
     * Inicializa as instâncias do modelo ClienteModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
        $this->logger = new Logger();
        $this->emailSender = new EmailSender();
        $this->configs = require dirname(__DIR__, 2) . '/src/Configs/config.php';
    }

    /**
     * Método para criar um novo cliente.
     *
     * @param array $dados Associativo com os dados do cliente a serem inseridos. Campos obrigatórios: 
     *                     cliente_nome, cliente_email, cliente_telefone, cliente_ativo, 
     *                     cliente_assinaturas, cliente_deputado_id, cliente_deputado_nome, 
     *                     cliente_deputado_estado, cliente_cpf.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarCliente($dados)
    {
        $camposObrigatorios = ['cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_assinaturas',  'cliente_deputado_nome', 'cliente_deputado_estado', 'cliente_cpf', 'cliente_deputado_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        try {
            $this->clienteModel->criar($dados);
            $buscaUltimo = $this->listarClientes();
            $clienteid = $buscaUltimo['dados'][0]['cliente_id'];


            $mensagem = '
           <!DOCTYPE html>
                    <html lang="pt-BR">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Bem-vindo!</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f4f4f4;
                                margin: 0;
                                padding: 0;
                            }
                            .container {
                                width: 100%;
                                max-width: 600px;
                                margin: 20px auto;
                                background: #ffffff;
                                padding: 20px;
                                border-radius: 8px;
                                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                            }
                            h1 {
                                color: #333;
                            }
                            p {
                                color: #555;
                                line-height: 1.5;
                            }
                            .button {
                                display: inline-block;
                                background: #007bff;
                                color: #ffffff;
                                padding: 10px 20px;
                                text-decoration: none;
                                border-radius: 5px;
                                margin-top: 10px;
                            }
                            .footer {
                                margin-top: 20px;
                                font-size: 12px;
                                color: #888;
                                text-align: center;
                            }
                        </style>
                    </head>
                    <body>

                        <div class="container">
                            <h1>Olá, '.$dados['cliente_nome'].'!</h1>
                            <p>Seja bem-vindo ao <strong>Gabinete Digital</strong>. Encaminhe esse email para os usuários do gabinete que irão acessar o sistema.</p>
                            <p>
                                <a href="'.$this->configs['app']['base_url'] . '?secao=novo-usuario&token=' . $clienteid.'" class="button" style="color:white">Acessar minha conta</a>
                            </p>
                            <p>Se você não solicitou este acesso, ignore este e-mail.</p>
                            <div class="footer">
                                <p>&copy; 2025 Gabinete Digital. Todos os direitos reservados.</p>
                            </div>
                        </div>

                    </body>
                    </html>

            
            ';


            $this->emailSender->sendEmail($dados['cliente_email'], 'Instruções para acesso', $mensagem, 'Instruções de acesso');
            return ['status' => 'success', 'message' => 'Cliente inserido com sucesso. Em breve você receberá um email com as instruções para acesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'E-mail ou CPF já está cadastrado ou já existe uma assinatura para esse deputado.'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    /**
     * Método para atualizar os dados de um cliente existente.
     *
     * @param int $cliente_id ID do cliente a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do cliente. Campos obrigatórios: 
     *                     cliente_nome, cliente_email, cliente_telefone, cliente_ativo, 
     *                     cliente_assinaturas, cliente_deputado, cliente_deputado_id, 
     *                     cliente_deputado_nome.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarCliente($cliente_id, $dados)
    {
        $camposObrigatorios = ['cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_assinaturas', 'cliente_deputado',  'cliente_deputado_nome', 'cliente_deputado_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        try {
            $this->clienteModel->atualizar($cliente_id, $dados);
            return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os clientes registrados.
     *
     * @return array Retorna um array com o status da operação, mensagem e lista de clientes.
     */
    public function listarClientes()
    {
        try {
            $busca = $this->clienteModel->listar();

            if (empty($busca)) {
                return ['status' => 'empty', 'message' => 'Nenhum cliente registrado'];
            }

            return ['status' => 'success', 'message' => count($busca) . ' cliente(s) encontrado(s)', 'dados' => $busca];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um cliente específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados do cliente ou mensagem de cliente não encontrado.
     */
    public function buscarCliente($coluna, $valor)
    {

        $colunasPermitidas = ['cliente_id', 'cliente_email'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas cliente_id e cliente_email são permitidos.'];
        }

        try {
            $cliente = $this->clienteModel->buscar($coluna, $valor);
            if ($cliente) {
                return ['status' => 'success', 'dados' => $cliente];
            } else {
                return ['status' => 'not_found', 'message' => 'Cliente não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um cliente.
     *
     * @param int $cliente_id ID do cliente a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarCliente($cliente_id)
    {
        try {
            $result = $this->buscarCliente('cliente_id', $cliente_id);

            if ($result['status'] == 'not_found') {
                return $result;
            }

            $this->clienteModel->apagar($cliente_id);
            return ['status' => 'success', 'message' => 'Cliente apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar o cliente. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para desativar um cliente e seus usuários associados.
     *
     * @param string $cliente_id ID do cliente a ter seu status alterado.
     * @param int $status Novo status do cliente.
     * @return array Retorna um array com o status da operação, mensagem de sucesso, erro ou status inválido.
     */
    public function mudarStatusCliente($cliente_id, $status)
    {

        if ($status === null || $status === '') {
            return ['status' => 'bad_request', 'message' => 'Status não pode ser nulo ou vazio.'];
        }

        if (!in_array($status, [0, 1])) {
            return ['status' => 'bad_request', 'message' => 'Status inválido.'];
        }

        $result = $this->buscarCliente('cliente_id', $cliente_id);

        if ($result['status'] == 'not_found') {
            return $result;
        }

        try {
            $this->clienteModel->mudarStatusCliente($cliente_id, $status);
            return ['status' => 'success', 'message' => 'Status do cliente alterado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

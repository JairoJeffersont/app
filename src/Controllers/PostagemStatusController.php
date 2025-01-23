<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\PostagemStatusModel;
use PDOException;

/**
 * Classe PostagemStatusController
 *
 * Controla as operações relacionadas ao status das postagens, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class PostagemStatusController
{

    /**
     * @var PostagemStatusModel Instância do modelo PostagemStatusModel para interagir com os dados.
     */
    private $postagemStatusModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do PostagemStatusController.
     *
     * Inicializa as instâncias do modelo PostagemStatusModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->postagemStatusModel = new PostagemStatusModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar um novo status de postagem.
     *
     * @param array $dados Associativo com os dados do status de postagem a serem inseridos. Campos obrigatórios:
     *                     postagem_status_nome, postagem_status_descricao, postagem_status_criado_por, postagem_status_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarPostagemStatus($dados)
    {
        $camposObrigatorios = ['postagem_status_nome', 'postagem_status_criado_por', 'postagem_status_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->postagemStatusModel->criar($dados);
            return ['status' => 'success', 'message' => 'Status de postagem criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do status de postagem já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um status de postagem existente.
     *
     * @param string $postagem_status_id ID do status de postagem a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do status de postagem.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarPostagemStatus($postagem_status_id, $dados)
    {
        $camposObrigatorios = ['postagem_status_nome'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $postagemStatus = $this->buscarPostagemStatus('postagem_status_id', $postagem_status_id);

        if ($postagemStatus['status'] == 'not_found') {
            return $postagemStatus;
        }

        if ($postagemStatus['dados'][0]['postagem_status_cliente'] == 1) {
            return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o status padrão.'];
        }

        try {
            $this->postagemStatusModel->atualizar($postagem_status_id, $dados);
            return ['status' => 'success', 'message' => 'Status de postagem atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os status de postagens registrados de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de status de postagens.
     */
    public function listarPostagensStatus($cliente)
    {
        try {
            $postagensStatus = $this->postagemStatusModel->listar($cliente);

            if (empty($postagensStatus)) {
                return ['status' => 'empty', 'message' => 'Nenhum status de postagem registrado.'];
            }

            return ['status' => 'success', 'message' => count($postagensStatus) . ' status(s) de postagem(s) encontrado(s)', 'dados' => $postagensStatus];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um status de postagem específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarPostagemStatus($coluna, $valor)
    {
        $colunasPermitidas = ['postagem_status_id', 'postagem_status_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas postagem_status_id e postagem_status_nome são permitidos.'];
        }

        try {
            $postagemStatus = $this->postagemStatusModel->buscar($coluna, $valor);
            if ($postagemStatus) {
                return ['status' => 'success', 'dados' => $postagemStatus];
            } else {
                return ['status' => 'not_found', 'message' => 'Status de postagem não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um status de postagem.
     *
     * @param string $postagem_status_id ID do status de postagem a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarPostagemStatus($postagem_status_id)
    {
        try {
            $postagemStatus = $this->buscarPostagemStatus('postagem_status_id', $postagem_status_id);

            if ($postagemStatus['status'] == 'not_found') {
                return $postagemStatus;
            }
            
            if ($postagemStatus['dados'][0]['postagem_status_cliente'] == 1) {
                return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o status padrão.'];
            }

            $this->postagemStatusModel->apagar($postagem_status_id);
            return ['status' => 'success', 'message' => 'Status de postagem apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o status de postagem. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

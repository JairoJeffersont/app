<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\ClippingTipoModel;
use PDOException;

/**
 * Classe ClippingTipoController
 *
 * Controla as operações relacionadas a tipos de clipping, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class ClippingTipoController
{

    /**
     * @var ClippingTipoModel Instância do modelo ClippingTipoModel para interagir com os dados.
     */
    private $clippingTipoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do ClippingTipoController.
     *
     * Inicializa as instâncias do modelo ClippingTipoModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->clippingTipoModel = new ClippingTipoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar um novo tipo de clipping.
     *
     * @param array $dados Associativo com os dados do tipo de clipping a serem inseridos. Campos obrigatórios:
     *                     clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarClippingTipo($dados)
    {
        $camposObrigatorios = ['clipping_tipo_nome', 'clipping_tipo_descricao', 'clipping_tipo_criado_por', 'clipping_tipo_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->clippingTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de clipping criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do tipo de clipping já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um tipo de clipping existente.
     *
     * @param string $clipping_tipo_id ID do tipo de clipping a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do tipo de clipping.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarClippingTipo($clipping_tipo_id, $dados)
    {
        $camposObrigatorios = ['clipping_tipo_nome', 'clipping_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $clippingTipo = $this->buscarClippingTipo('clipping_tipo_id', $clipping_tipo_id);

        if ($clippingTipo['status'] == 'not_found') {
            return $clippingTipo;
        }

        if ($clippingTipo['dados'][0]['clipping_tipo_cliente'] == 1) {
            return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de clipping padrão.'];
        }

        try {
            $this->clippingTipoModel->atualizar($clipping_tipo_id, $dados);
            return ['status' => 'success', 'message' => 'Tipo de clipping atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os tipos de clipping registrados de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de tipos de clipping.
     */
    public function listarClippingTipos($cliente)
    {
        try {
            $clippingTipos = $this->clippingTipoModel->listar($cliente);

            if (empty($clippingTipos)) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de clipping registrado.'];
            }

            return ['status' => 'success', 'message' => count($clippingTipos) . ' tipo(s) de clipping encontrado(s)', 'dados' => $clippingTipos];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um tipo de clipping específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarClippingTipo($coluna, $valor)
    {
        $colunasPermitidas = ['clipping_tipo_id', 'clipping_tipo_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas clipping_tipo_id e clipping_tipo_nome são permitidos.'];
        }

        try {
            $clippingTipo = $this->clippingTipoModel->buscar($coluna, $valor);
            if ($clippingTipo) {
                return ['status' => 'success', 'dados' => $clippingTipo];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de clipping não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um tipo de clipping.
     *
     * @param string $clipping_tipo_id ID do tipo de clipping a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarClippingTipo($clipping_tipo_id)
    {
        try {
            $clippingTipo = $this->buscarClippingTipo('clipping_tipo_id', $clipping_tipo_id);

            if ($clippingTipo['status'] == 'not_found') {
                return $clippingTipo;
            }

            if ($clippingTipo['dados'][0]['clipping_tipo_cliente'] == 1) {
                return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de clipping padrão.'];
            }

            $this->clippingTipoModel->apagar($clipping_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de clipping apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o tipo de clipping. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

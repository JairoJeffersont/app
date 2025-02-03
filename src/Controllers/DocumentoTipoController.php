<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\DocumentoTipoModel;
use PDOException;

/**
 * Classe DocumentoTipoController
 *
 * Controla as operações relacionadas a tipos de documentos, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class DocumentoTipoController {

    /**
     * @var DocumentoTipoModel Instância do modelo DocumentoTipoModel para interagir com os dados.
     */
    private $documentoTipoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do DocumentoTipoController.
     *
     * Inicializa as instâncias do modelo DocumentoTipoModel e do Logger para gerenciamento de logs.
     */
    public function __construct() {
        $this->documentoTipoModel = new DocumentoTipoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar um novo tipo de documento.
     *
     * @param array $dados Associativo com os dados do tipo de documento a serem inseridos. Campos obrigatórios:
     *                     documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarDocumentoTipo($dados) {
        $camposObrigatorios = ['documento_tipo_nome', 'documento_tipo_descricao', 'documento_tipo_criado_por', 'documento_tipo_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->documentoTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de documento criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do tipo de documento já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('documento_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um tipo de documento existente.
     *
     * @param string $documento_tipo_id ID do tipo de documento a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do tipo de documento.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarDocumentoTipo($documento_tipo_id, $dados) {
        $camposObrigatorios = ['documento_tipo_nome', 'documento_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $documentoTipo = $this->buscarDocumentoTipo('documento_tipo_id', $documento_tipo_id);

        if ($documentoTipo['status'] == 'not_found') {
            return $documentoTipo;
        }

        if ($documentoTipo['dados'][0]['documento_tipo_cliente'] == 1) {
            return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de documento padrão.'];
        }

        try {
            $this->documentoTipoModel->atualizar($documento_tipo_id, $dados);
            return ['status' => 'success', 'message' => 'Tipo de documento atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os tipos de documentos registrados de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de tipos de documentos.
     */
    public function listarDocumentosTipos($cliente) {
        try {
            $documentosTipos = $this->documentoTipoModel->listar($cliente);

            if (empty($documentosTipos)) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de documento registrado.'];
            }

            return ['status' => 'success', 'message' => count($documentosTipos) . ' tipo(s) de documento(s) encontrado(s)', 'dados' => $documentosTipos];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um tipo de documento específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarDocumentoTipo($coluna, $valor) {
        $colunasPermitidas = ['documento_tipo_id', 'documento_tipo_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas documento_tipo_id e documento_tipo_nome são permitidos.'];
        }

        try {
            $documentoTipo = $this->documentoTipoModel->buscar($coluna, $valor);
            if ($documentoTipo) {
                return ['status' => 'success', 'dados' => $documentoTipo];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de documento não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um tipo de documento.
     *
     * @param string $documento_tipo_id ID do tipo de documento a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarDocumentoTipo($documento_tipo_id) {
        try {
            $documentoTipo = $this->buscarDocumentoTipo('documento_tipo_id', $documento_tipo_id);

            if ($documentoTipo['status'] == 'not_found') {
                return $documentoTipo;
            }

            if ($documentoTipo['dados'][0]['documento_tipo_cliente'] == 1) {
                return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de documento padrão.'];
            }

            $this->documentoTipoModel->apagar($documento_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de documento apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o tipo de documento. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('documento_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

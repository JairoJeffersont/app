<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\OrgaoTipoModel;
use PDOException;

/**
 * Classe OrgaoTipoController
 *
 * Controla as operações relacionadas a tipos de órgãos, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class OrgaoTipoController
{

    /**
     * @var OrgaoTipoModel Instância do modelo OrgaoTipoModel para interagir com os dados.
     */
    private $orgaoTipoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do OrgaoTipoController.
     *
     * Inicializa as instâncias do modelo OrgaoTipoModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->orgaoTipoModel = new OrgaoTipoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar um novo tipo de órgão.
     *
     * @param array $dados Associativo com os dados do tipo de órgão a serem inseridos. Campos obrigatórios:
     *                     orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarOrgaoTipo($dados)
    {
        $camposObrigatorios = ['orgao_tipo_nome', 'orgao_tipo_descricao', 'orgao_tipo_criado_por', 'orgao_tipo_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->orgaoTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do tipo de órgão já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um tipo de órgão existente.
     *
     * @param string $orgao_tipo_id ID do tipo de órgão a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do tipo de órgão.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarOrgaoTipo($orgao_tipo_id, $dados)
    {
        $camposObrigatorios = ['orgao_tipo_nome', 'orgao_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $orgaoTipo = $this->buscarOrgaoTipo('orgao_tipo_id', $orgao_tipo_id);

        if ($orgaoTipo['status'] == 'not_found') {
            return $orgaoTipo;
        }

        if ($orgaoTipo['dados'][0]['orgao_tipo_cliente'] == 1) {
            return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de órgão padrão.'];
        }

        try {
            $this->orgaoTipoModel->atualizar($orgao_tipo_id, $dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os tipos de órgãos registrados de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de tipos de órgãos.
     */
    public function listarOrgaosTipos($cliente)
    {
        try {
            $orgaosTipos = $this->orgaoTipoModel->listar($cliente);

            if (empty($orgaosTipos)) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de órgão registrado.'];
            }

            return ['status' => 'success', 'message' => count($orgaosTipos) . ' tipo(s) de órgão(s) encontrado(s)', 'dados' => $orgaosTipos];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um tipo de órgão específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarOrgaoTipo($coluna, $valor)
    {
        $colunasPermitidas = ['orgao_tipo_id', 'orgao_tipo_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas orgao_tipo_id e orgao_tipo_nome são permitidos.'];
        }

        try {
            $orgaoTipo = $this->orgaoTipoModel->buscar($coluna, $valor);
            if ($orgaoTipo) {
                return ['status' => 'success', 'dados' => $orgaoTipo];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um tipo de órgão.
     *
     * @param string $orgao_tipo_id ID do tipo de órgão a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarOrgaoTipo($orgao_tipo_id)
    {
        try {
            $orgaoTipo = $this->buscarOrgaoTipo('orgao_tipo_id', $orgao_tipo_id);

            if ($orgaoTipo['status'] == 'not_found') {
                return $orgaoTipo;
            }

            if ($orgaoTipo['dados'][0]['orgao_tipo_cliente'] == 1) {
                return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de órgão padrão.'];
            }

            $this->orgaoTipoModel->apagar($orgao_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de órgão apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o tipo de órgão. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

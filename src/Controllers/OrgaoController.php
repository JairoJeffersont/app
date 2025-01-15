<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\OrgaoModel;
use PDOException;

/**
 * Classe OrgaoController
 *
 * Controla as operações relacionadas a órgãos, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class OrgaoController
{

    /**
     * @var OrgaoModel Instância do modelo OrgaoModel para interagir com os dados.
     */
    private $orgaoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do OrgaoController.
     *
     * Inicializa as instâncias do modelo OrgaoModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->orgaoModel = new OrgaoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar um novo órgão.
     *
     * @param array $dados Associativo com os dados do órgão a serem inseridos. Campos obrigatórios:
     *                     orgao_nome, orgao_email, orgao_municipio, orgao_estado, orgao_tipo, orgao_criado_por, orgao_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarOrgao($dados)
    {
        $camposObrigatorios = ['orgao_nome', 'orgao_email', 'orgao_municipio', 'orgao_estado', 'orgao_tipo', 'orgao_criado_por', 'orgao_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->orgaoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Órgão criado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um órgão existente.
     *
     * @param string $orgao_id ID do órgão a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do órgão.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarOrgao($orgao_id, $dados)
    {
        $camposObrigatorios = [
            'orgao_nome',
            'orgao_email',
            'orgao_telefone',
            'orgao_endereco',
            'orgao_bairro',
            'orgao_municipio',
            'orgao_estado',
            'orgao_cep',
            'orgao_tipo'
        ];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $orgao = $this->buscarOrgao('orgao_id', $orgao_id);

        if ($orgao['status'] == 'not_found') {
            return $orgao;
        }

        try {
            $this->orgaoModel->atualizar($orgao_id, $dados);
            return ['status' => 'success', 'message' => 'Órgão atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    /**
     * Método para listar órgãos com filtros e paginação.
     *
     * @param int $itens Número de itens por página.
     * @param int $pagina Número da página.
     * @param string $ordem Ordem de classificação (ASC ou DESC).
     * @param string $ordenarPor Coluna para ordenação.
     * @param string|null $termo Termo de busca (opcional).
     * @param string|null $estado Filtro por estado (opcional).
     * @param int $cliente ID do cliente associado.
     * @return array Retorna um array com o status da operação, mensagem e lista de órgãos.
     */
    public function listarOrgaos($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente)
    {
        try {
            $result = $this->orgaoModel->listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente);

            $total = (isset($result[0]['total'])) ? $result[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhum órgão encontrado.'];
            }

            return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $result];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    /**
     * Método para buscar um órgão específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarOrgao($coluna, $valor)
    {
        try {
            $orgao = $this->orgaoModel->buscar($coluna, $valor);
            if ($orgao) {
                return ['status' => 'success', 'dados' => $orgao];
            } else {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    /**
     * Método para apagar um órgão.
     *
     * @param string $orgao_id ID do órgão a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarOrgao($orgao_id)
    {
        try {
            $orgao = $this->buscarOrgao('orgao_id', $orgao_id);

            if ($orgao['status'] == 'not_found') {
                return $orgao;
            }

            $this->orgaoModel->apagar($orgao_id);
            return ['status' => 'success', 'message' => 'Órgão apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o órgão. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }
}

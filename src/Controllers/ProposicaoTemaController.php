<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\ProposicaoTemaModel;
use PDOException;

/**
 * Classe ProposicaoTemaController
 *
 * Controla as operações relacionadas aos proposições de temas, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class ProposicaoTemaController
{

    /**
     * @var ProposicaoTemaModel Instância do modelo ProposicaoTemaModel para interagir com os dados.
     */
    private $proposicaoTemaModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do ProposicaoTemaController.
     *
     * Inicializa as instâncias do modelo ProposicaoTemaModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->proposicaoTemaModel = new ProposicaoTemaModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar um novo proposição de tema.
     *
     * @param array $dados Associativo com os dados do proposição de tema a serem inseridos. Campos obrigatórios:
     *                     proposicao_tema_nome, proposicao_tema_criado_por, proposicao_tema_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarProposicaoTema($dados)
    {
        $camposObrigatorios = ['proposicao_tema_nome', 'proposicao_tema_criado_por', 'proposicao_tema_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->proposicaoTemaModel->criar($dados);
            return ['status' => 'success', 'message' => 'Proposição de tema criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do proposição de tema já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tema_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um proposição de tema existente.
     *
     * @param string $proposicao_tema_id ID do proposição de tema a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do proposição de tema.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarProposicaoTema($proposicao_tema_id, $dados)
    {
        $camposObrigatorios = ['proposicao_tema_nome'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $proposicaoTema = $this->buscarProposicaoTema('proposicao_tema_id', $proposicao_tema_id);

        if ($proposicaoTema['status'] == 'not_found') {
            return $proposicaoTema;
        }

        try {
            $this->proposicaoTemaModel->atualizar($proposicao_tema_id, $dados);
            return ['status' => 'success', 'message' => 'Proposição de tema atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tema_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os proposições de temas registrados de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de proposições de temas.
     */
    public function listarProposicoesTemas($cliente)
    {
        try {
            $proposicoesTemas = $this->proposicaoTemaModel->listar($cliente);

            if (empty($proposicoesTemas)) {
                return ['status' => 'empty', 'message' => 'Nenhum proposição de tema registrado.'];
            }

            return ['status' => 'success', 'message' => count($proposicoesTemas) . ' proposição(ões) de tema(s) encontrado(s)', 'dados' => $proposicoesTemas];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tema_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um proposição de tema específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarProposicaoTema($coluna, $valor)
    {
        $colunasPermitidas = ['proposicao_tema_id', 'proposicao_tema_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas proposicao_tema_id e proposicao_tema_nome são permitidos.'];
        }

        try {
            $proposicaoTema = $this->proposicaoTemaModel->buscar($coluna, $valor);
            if ($proposicaoTema) {
                return ['status' => 'success', 'dados' => $proposicaoTema];
            } else {
                return ['status' => 'not_found', 'message' => 'Proposição de tema não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tema_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um proposição de tema.
     *
     * @param string $proposicao_tema_id ID do proposição de tema a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarProposicaoTema($proposicao_tema_id)
    {
        try {
            $proposicaoTema = $this->buscarProposicaoTema('proposicao_tema_id', $proposicao_tema_id);

            if ($proposicaoTema['status'] == 'not_found') {
                return $proposicaoTema;
            }

            $this->proposicaoTemaModel->apagar($proposicao_tema_id);
            return ['status' => 'success', 'message' => 'Proposição de tema apagada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a proposição de tema. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tema_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

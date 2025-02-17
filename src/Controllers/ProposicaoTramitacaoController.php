<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\ProposicaoTramitacaoModel;
use PDOException;

/**
 * Classe ProposicaoTramitacaoController
 *
 * Controla as operações relacionadas a proposições de trâmitação, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class ProposicaoTramitacaoController
{

    /**
     * @var ProposicaoTramitacaoModel Instância do modelo ProposicaoTramitacaoModel para interagir com os dados.
     */
    private $proposicaoTramitacaoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do ProposicaoTramitacaoController.
     *
     * Inicializa as instâncias do modelo ProposicaoTramitacaoModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->proposicaoTramitacaoModel = new ProposicaoTramitacaoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar uma nova proposição de trâmitação.
     *
     * @param array $dados Associativo com os dados da proposição de trâmitação a serem inseridos. Campos obrigatórios:
     *                     proposicao_tramitacao_nome, proposicao_tramitacao_descricao, proposicao_tramitacao_criada_por, proposicao_tramitacao_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarProposicaoTramitacao($dados)
    {
        $camposObrigatorios = ['proposicao_tramitacao_nome', 'proposicao_tramitacao_descricao', 'proposicao_tramitacao_criada_por', 'proposicao_tramitacao_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->proposicaoTramitacaoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Proposição de trâmitação criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'A descrição da proposição de trâmitação já está cadastrada.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tramitacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de uma proposição de trâmitação existente.
     *
     * @param string $proposicao_tramitacao_id ID da proposição de trâmitação a ser atualizada.
     * @param array $dados Associativo com os dados atualizados da proposição de trâmitação.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarProposicaoTramitacao($proposicao_tramitacao_id, $dados)
    {
        $camposObrigatorios = ['proposicao_tramitacao_nome', 'proposicao_tramitacao_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $proposicaoTramitacao = $this->buscarProposicaoTramitacao('proposicao_tramitacao_id', $proposicao_tramitacao_id);

        if ($proposicaoTramitacao['status'] == 'not_found') {
            return $proposicaoTramitacao;
        }

        try {
            $this->proposicaoTramitacaoModel->atualizar($proposicao_tramitacao_id, $dados);
            return ['status' => 'success', 'message' => 'Proposição de trâmitação atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tramitacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todas as proposições de trâmitação registradas de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de proposições de trâmitação.
     */
    public function listarProposicoesTramitacao($cliente)
    {
        try {
            $proposicoesTramitacao = $this->proposicaoTramitacaoModel->listar($cliente);

            if (empty($proposicoesTramitacao)) {
                return ['status' => 'empty', 'message' => 'Nenhuma proposição de trâmitação registrada.'];
            }

            return ['status' => 'success', 'message' => count($proposicoesTramitacao) . ' proposição(ões) de trâmitação encontrada(s)', 'dados' => $proposicoesTramitacao];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tramitacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar uma proposição de trâmitação específica baseada em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarProposicaoTramitacao($coluna, $valor)
    {
        $colunasPermitidas = ['proposicao_tramitacao_id', 'proposicao_tramitacao_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas proposicao_tramitacao_id e proposicao_tramitacao_nome são permitidos.'];
        }

        try {
            $proposicaoTramitacao = $this->proposicaoTramitacaoModel->buscar($coluna, $valor);
            if ($proposicaoTramitacao) {
                return ['status' => 'success', 'dados' => $proposicaoTramitacao];
            } else {
                return ['status' => 'not_found', 'message' => 'Proposição de trâmitação não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tramitacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar uma proposição de trâmitação.
     *
     * @param string $proposicao_tramitacao_id ID da proposição de trâmitação a ser apagada.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarProposicaoTramitacao($proposicao_tramitacao_id)
    {
        try {
            $proposicaoTramitacao = $this->buscarProposicaoTramitacao('proposicao_tramitacao_id', $proposicao_tramitacao_id);

            if ($proposicaoTramitacao['status'] == 'not_found') {
                return $proposicaoTramitacao;
            }

            $this->proposicaoTramitacaoModel->apagar($proposicao_tramitacao_id);
            return ['status' => 'success', 'message' => 'Proposição de trâmitação apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a proposição de trâmitação. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_tramitacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

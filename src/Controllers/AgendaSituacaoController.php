<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\AgendaSituacaoModel;
use PDOException;

/**
 * Classe AgendaSituacaoController
 *
 * Controla as operações relacionadas a situações de agenda, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class AgendaSituacaoController
{

    /**
     * @var AgendaSituacaoModel Instância do modelo AgendaSituacaoModel para interagir com os dados.
     */
    private $agendaSituacaoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do AgendaSituacaoController.
     *
     * Inicializa as instâncias do modelo AgendaSituacaoModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->agendaSituacaoModel = new AgendaSituacaoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar uma nova situação de agenda.
     *
     * @param array $dados Associativo com os dados da situação de agenda a serem inseridos. Campos obrigatórios:
     *                     agenda_situacao_nome, agenda_situacao_descricao, agenda_situacao_criado_por, agenda_situacao_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarAgendaSituacao($dados)
    {
        $camposObrigatorios = ['agenda_situacao_nome', 'agenda_situacao_descricao', 'agenda_situacao_criado_por', 'agenda_situacao_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->agendaSituacaoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Situação de agenda criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome da situação de agenda já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de uma situação de agenda existente.
     *
     * @param string $agenda_situacao_id ID da situação de agenda a ser atualizado.
     * @param array $dados Associativo com os dados atualizados da situação de agenda.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarAgendaSituacao($agenda_situacao_id, $dados)
    {
        $camposObrigatorios = ['agenda_situacao_nome', 'agenda_situacao_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $agendaSituacao = $this->buscarAgendaSituacao('agenda_situacao_id', $agenda_situacao_id);

        if ($agendaSituacao['status'] == 'not_found') {
            return $agendaSituacao;
        }

        try {
            $this->agendaSituacaoModel->atualizar($agenda_situacao_id, $dados);
            return ['status' => 'success', 'message' => 'Situação de agenda atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todas as situações de agenda registradas de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de situações de agenda.
     */
    public function listarAgendaSituacoes($cliente)
    {
        try {
            $agendaSituacoes = $this->agendaSituacaoModel->listar($cliente);

            if (empty($agendaSituacoes)) {
                return ['status' => 'empty', 'message' => 'Nenhuma situação de agenda registrada.'];
            }

            return ['status' => 'success', 'message' => count($agendaSituacoes) . ' situação(ões) de agenda encontrada(s)', 'dados' => $agendaSituacoes];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar uma situação de agenda específica baseada em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarAgendaSituacao($coluna, $valor)
    {
        $colunasPermitidas = ['agenda_situacao_id', 'agenda_situacao_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas agenda_situacao_id e agenda_situacao_nome são permitidos.'];
        }

        try {
            $agendaSituacao = $this->agendaSituacaoModel->buscar($coluna, $valor);
            if ($agendaSituacao) {
                return ['status' => 'success', 'dados' => $agendaSituacao];
            } else {
                return ['status' => 'not_found', 'message' => 'Situação de agenda não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar uma situação de agenda.
     *
     * @param string $agenda_situacao_id ID da situação de agenda a ser apagada.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarAgendaSituacao($agenda_situacao_id)
    {
        try {
            $agendaSituacao = $this->buscarAgendaSituacao('agenda_situacao_id', $agenda_situacao_id);

            if ($agendaSituacao['status'] == 'not_found') {
                return $agendaSituacao;
            }

            $this->agendaSituacaoModel->apagar($agenda_situacao_id);
            return ['status' => 'success', 'message' => 'Situação de agenda apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a situação de agenda. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('agenda_situacao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

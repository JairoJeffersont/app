<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\AgendaTipoModel;
use PDOException;

/**
 * Classe AgendaTipoController
 *
 * Controla as operações relacionadas a tipos de agenda, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class AgendaTipoController
{

    /**
     * @var AgendaTipoModel Instância do modelo AgendaTipoModel para interagir com os dados.
     */
    private $agendaTipoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do AgendaTipoController.
     *
     * Inicializa as instâncias do modelo AgendaTipoModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->agendaTipoModel = new AgendaTipoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar um novo tipo de agenda.
     *
     * @param array $dados Associativo com os dados do tipo de agenda a serem inseridos. Campos obrigatórios:
     *                     agenda_tipo_nome, agenda_tipo_descricao, agenda_tipo_criado_por, agenda_tipo_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarAgendaTipo($dados)
    {
        $camposObrigatorios = ['agenda_tipo_nome', 'agenda_tipo_descricao', 'agenda_tipo_criado_por', 'agenda_tipo_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->agendaTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de agenda criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do tipo de agenda já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um tipo de agenda existente.
     *
     * @param string $agenda_tipo_id ID do tipo de agenda a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do tipo de agenda.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarAgendaTipo($agenda_tipo_id, $dados)
    {
        $camposObrigatorios = ['agenda_tipo_nome', 'agenda_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $agendaTipo = $this->buscarAgendaTipo('agenda_tipo_id', $agenda_tipo_id);

        if ($agendaTipo['status'] == 'not_found') {
            return $agendaTipo;
        }

        if ($agendaTipo['dados'][0]['agenda_tipo_cliente'] == 1) {
            return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de agenda padrão.'];
        }

        try {
            $this->agendaTipoModel->atualizar($agenda_tipo_id, $dados);
            return ['status' => 'success', 'message' => 'Tipo de agenda atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os tipos de agenda registrados de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de tipos de agenda.
     */
    public function listarAgendaTipos($cliente)
    {
        try {
            $agendaTipos = $this->agendaTipoModel->listar($cliente);

            if (empty($agendaTipos)) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de agenda registrado.'];
            }

            return ['status' => 'success', 'message' => count($agendaTipos) . ' tipo(s) de agenda encontrado(s)', 'dados' => $agendaTipos];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um tipo de agenda específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarAgendaTipo($coluna, $valor)
    {
        $colunasPermitidas = ['agenda_tipo_id', 'agenda_tipo_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas agenda_tipo_id e agenda_tipo_nome são permitidos.'];
        }

        try {
            $agendaTipo = $this->agendaTipoModel->buscar($coluna, $valor);
            if ($agendaTipo) {
                return ['status' => 'success', 'dados' => $agendaTipo];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de agenda não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um tipo de agenda.
     *
     * @param string $agenda_tipo_id ID do tipo de agenda a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarAgendaTipo($agenda_tipo_id)
    {
        try {
            $agendaTipo = $this->buscarAgendaTipo('agenda_tipo_id', $agenda_tipo_id);

            if ($agendaTipo['status'] == 'not_found') {
                return $agendaTipo;
            }

            if ($agendaTipo['dados'][0]['agenda_tipo_cliente'] == 1) {
                return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de agenda padrão.'];
            }

            $this->agendaTipoModel->apagar($agenda_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de agenda apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o tipo de agenda. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('agenda_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\AgendaModel;
use PDOException;

/**
 * Classe AgendaController
 *
 * Controla as operações relacionadas a agendas, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class AgendaController
{
    /**
     * @var AgendaModel Instância do modelo AgendaModel para interagir com os dados.
     */
    private $agendaModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do AgendaController.
     *
     * Inicializa as instâncias do modelo AgendaModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->agendaModel = new AgendaModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar uma nova agenda.
     *
     * @param array $dados Associativo com os dados da agenda a serem inseridos. Campos obrigatórios:
     *                     agenda_titulo, agenda_situacao, agenda_tipo, agenda_data, agenda_local,
     *                     agenda_estado, agenda_criada_por, agenda_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarAgenda($dados)
    {
        $camposObrigatorios = ['agenda_titulo', 'agenda_situacao', 'agenda_tipo', 'agenda_data', 'agenda_local', 'agenda_estado', 'agenda_criada_por', 'agenda_cliente'];
    
        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }
    
        $dataAtual = date('Y-m-d');
        $dataAgenda = date('Y-m-d', strtotime($dados['agenda_data']));
    
        if ($dataAgenda < $dataAtual) {
            return ['status' => 'bad_request', 'message' => "A data da agenda não pode ser no passado."];
        }
    
        try {
            $this->agendaModel->criar($dados);
            return ['status' => 'success', 'message' => 'Agenda criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Já existe uma agenda com esse título.'];
            }
    
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
    

    /**
     * Método para atualizar os dados de uma agenda existente.
     *
     * @param string $agenda_id ID da agenda a ser atualizado.
     * @param array $dados Associativo com os dados atualizados da agenda.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarAgenda($agenda_id, $dados)
    {
        $camposObrigatorios = ['agenda_titulo', 'agenda_situacao', 'agenda_tipo', 'agenda_data', 'agenda_local', 'agenda_estado'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $agenda = $this->buscarAgenda('agenda_id', $agenda_id);

        if ($agenda['status'] == 'not_found') {
            return $agenda;
        }

        try {
            $this->agendaModel->atualizar($agenda_id, $dados);
            return ['status' => 'success', 'message' => 'Agenda atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todas as agendas registradas de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de agendas.
     */
    public function listarAgendas($data, $tipo, $situacao,  $cliente)
    {
        try {
            $agendas = $this->agendaModel->listar($data, $tipo, $situacao,  $cliente);

            if (empty($agendas)) {
                return ['status' => 'empty', 'message' => 'Nenhuma agenda registrada.'];
            }

            return ['status' => 'success', 'message' => count($agendas) . ' agenda(s) encontrada(s)', 'dados' => $agendas];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar uma agenda específica baseada em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarAgenda($coluna, $valor)
    {
        $colunasPermitidas = ['agenda_id', 'agenda_titulo'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas agenda_id e agenda_titulo são permitidos.'];
        }

        try {
            $agenda = $this->agendaModel->buscar($coluna, $valor);
            if ($agenda) {
                return ['status' => 'success', 'dados' => $agenda];
            } else {
                return ['status' => 'not_found', 'message' => 'Agenda não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar uma agenda.
     *
     * @param string $agenda_id ID da agenda a ser apagada.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarAgenda($agenda_id)
    {
        try {
            $agenda = $this->buscarAgenda('agenda_id', $agenda_id);

            if ($agenda['status'] == 'not_found') {
                return $agenda;
            }

            $this->agendaModel->apagar($agenda_id);
            return ['status' => 'success', 'message' => 'Agenda apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a agenda. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('agenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

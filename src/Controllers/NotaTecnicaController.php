<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\NotaTecnicaModel;
use PDOException;

/**
 * Classe NotaTecnicaController
 *
 * Controla as operações relacionadas às notas técnicas, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class NotaTecnicaController
{

    /**
     * @var NotaTecnicaModel Instância do modelo NotaTecnicaModel para interagir com os dados.
     */
    private $notaTecnicaModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do NotaTecnicaController.
     *
     * Inicializa as instâncias do modelo NotaTecnicaModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->notaTecnicaModel = new NotaTecnicaModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar uma nova nota técnica.
     *
     * @param array $dados Associativo com os dados da nota técnica a serem inseridos. Campos obrigatórios:
     *                     nota_proposicao, nota_proposicao_apelido, nota_proposicao_resumo, nota_texto, nota_criada_por, nota_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarNotaTecnica($dados)
    {
        $camposObrigatorios = ['nota_proposicao', 'nota_proposicao_apelido', 'nota_proposicao_resumo', 'nota_texto', 'nota_criada_por', 'nota_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->notaTecnicaModel->criar($dados);
            return ['status' => 'success', 'message' => 'Nota técnica criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'A nota técnica já está cadastrada.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de uma nota técnica existente.
     *
     * @param string $nota_id ID da nota técnica a ser atualizada.
     * @param array $dados Associativo com os dados atualizados da nota técnica.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarNotaTecnica($nota_id, $dados)
    {
        $camposObrigatorios = ['nota_proposicao', 'nota_proposicao_apelido', 'nota_proposicao_resumo', 'nota_texto'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $notaTecnica = $this->buscarNotaTecnica('nota_id', $nota_id);

        if ($notaTecnica['status'] == 'not_found') {
            return $notaTecnica;
        }

        try {
            $this->notaTecnicaModel->atualizar($nota_id, $dados);
            return ['status' => 'success', 'message' => 'Nota técnica atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todas as notas técnicas de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de notas técnicas.
     */
    public function listarNotasTecnicas($cliente)
    {
        try {
            $notasTecnicas = $this->notaTecnicaModel->listar($cliente);

            if (empty($notasTecnicas)) {
                return ['status' => 'empty', 'message' => 'Nenhuma nota técnica registrada.'];
            }

            return ['status' => 'success', 'message' => count($notasTecnicas) . ' nota(s) técnica(s) encontrada(s)', 'dados' => $notasTecnicas];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar uma nota técnica específica baseada em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarNotaTecnica($coluna, $valor)
    {
        $colunasPermitidas = ['nota_id', 'nota_proposicao'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas nota_id e nota_proposicao são permitidos.'];
        }

        try {
            $notaTecnica = $this->notaTecnicaModel->buscar($coluna, $valor);
            if ($notaTecnica) {
                return ['status' => 'success', 'dados' => $notaTecnica];
            } else {
                return ['status' => 'not_found', 'message' => 'Nota técnica não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar uma nota técnica.
     *
     * @param string $nota_id ID da nota técnica a ser apagada.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarNotaTecnica($nota_id)
    {
        try {
            $notaTecnica = $this->buscarNotaTecnica('nota_id', $nota_id);

            if ($notaTecnica['status'] == 'not_found') {
                return $notaTecnica;
            }

            $this->notaTecnicaModel->apagar($nota_id);
            return ['status' => 'success', 'message' => 'Nota técnica apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a nota técnica. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

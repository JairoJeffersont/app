<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\PessoaProfissaoModel;
use PDOException;

/**
 * Classe PessoaProfissaoController
 *
 * Controla as operações relacionadas às profissões de pessoas, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class PessoaProfissaoController {

    /**
     * @var PessoaProfissaoModel Instância do modelo PessoaProfissaoModel para interagir com os dados.
     */
    private $pessoaProfissaoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do PessoaProfissaoController.
     *
     * Inicializa as instâncias do modelo PessoaProfissaoModel e do Logger para gerenciamento de logs.
     */
    public function __construct() {
        $this->pessoaProfissaoModel = new PessoaProfissaoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar uma nova profissão.
     *
     * @param array $dados Associativo com os dados da profissão a serem inseridos. Campos obrigatórios:
     *                     pessoas_profissoes_nome, pessoas_profissoes_descricao, pessoas_profissoes_criado_por, pessoas_profissoes_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarProfissao($dados) {
        $camposObrigatorios = ['pessoas_profissoes_nome', 'pessoas_profissoes_descricao', 'pessoas_profissoes_criado_por', 'pessoas_profissoes_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->pessoaProfissaoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Profissão criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome da profissão já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de uma profissão existente.
     *
     * @param string $pessoas_profissoes_id ID da profissão a ser atualizada.
     * @param array $dados Associativo com os dados atualizados da profissão.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarProfissao($pessoas_profissoes_id, $dados) {
        $camposObrigatorios = ['pessoas_profissoes_nome', 'pessoas_profissoes_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $profissao = $this->buscarProfissao('pessoas_profissoes_id', $pessoas_profissoes_id);

        if ($profissao['status'] == 'not_found') {
            return $profissao;
        }

        try {
            $this->pessoaProfissaoModel->atualizar($pessoas_profissoes_id, $dados);
            return ['status' => 'success', 'message' => 'Profissão atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todas as profissões registradas de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de profissões.
     */
    public function listarProfissoes($cliente) {
        try {
            $profissoes = $this->pessoaProfissaoModel->listar($cliente);

            if (empty($profissoes)) {
                return ['status' => 'empty', 'message' => 'Nenhuma profissão registrada.'];
            }

            return ['status' => 'success', 'message' => count($profissoes) . ' profissão(ões) encontrada(s)', 'dados' => $profissoes];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar uma profissão específica baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarProfissao($coluna, $valor) {
        $colunasPermitidas = ['pessoas_profissoes_id', 'pessoas_profissoes_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas pessoas_profissoes_id e pessoas_profissoes_nome são permitidos.'];
        }

        try {
            $profissao = $this->pessoaProfissaoModel->buscar($coluna, $valor);
            if ($profissao) {
                return ['status' => 'success', 'dados' => $profissao];
            } else {
                return ['status' => 'not_found', 'message' => 'Profissão não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar uma profissão.
     *
     * @param string $pessoas_profissoes_id ID da profissão a ser apagada.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarProfissao($pessoas_profissoes_id) {
        try {
            $profissao = $this->buscarProfissao('pessoas_profissoes_id', $pessoas_profissoes_id);

            if ($profissao['status'] == 'not_found') {
                return $profissao;
            }

            $this->pessoaProfissaoModel->apagar($pessoas_profissoes_id);
            return ['status' => 'success', 'message' => 'Profissão apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a profissão. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

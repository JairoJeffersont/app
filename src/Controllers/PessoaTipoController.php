<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\PessoaTipoModel;
use PDOException;

/**
 * Classe PessoaTipoController
 *
 * Controla as operações relacionadas a tipos de pessoas, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class PessoaTipoController {

    /**
     * @var PessoaTipoModel Instância do modelo PessoaTipoModel para interagir com os dados.
     */
    private $pessoaTipoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do PessoaTipoController.
     *
     * Inicializa as instâncias do modelo PessoaTipoModel e do Logger para gerenciamento de logs.
     */
    public function __construct() {
        $this->pessoaTipoModel = new PessoaTipoModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar um novo tipo de pessoa.
     *
     * @param array $dados Associativo com os dados do tipo de pessoa a serem inseridos. Campos obrigatórios:
     *                     pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarPessoaTipo($dados) {
        $camposObrigatorios = ['pessoa_tipo_nome', 'pessoa_tipo_descricao', 'pessoa_tipo_criado_por', 'pessoa_tipo_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->pessoaTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de pessoa criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do tipo de pessoa já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um tipo de pessoa existente.
     *
     * @param string $pessoa_tipo_id ID do tipo de pessoa a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do tipo de pessoa.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarPessoaTipo($pessoa_tipo_id, $dados) {
        $camposObrigatorios = ['pessoa_tipo_nome', 'pessoa_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $pessoaTipo = $this->buscarPessoaTipo('pessoa_tipo_id', $pessoa_tipo_id);

        if ($pessoaTipo['status'] == 'not_found') {
            return $pessoaTipo;
        }

        if ($pessoaTipo['dados'][0]['pessoa_tipo_cliente'] == 1) {
            return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de pessoa padrão.'];
        }

        try {
            $this->pessoaTipoModel->atualizar($pessoa_tipo_id, $dados);
            return ['status' => 'success', 'message' => 'Tipo de pessoa atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os tipos de pessoas registrados de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de tipos de pessoas.
     */
    public function listarPessoasTipos($cliente) {
        try {
            $pessoasTipos = $this->pessoaTipoModel->listar($cliente);

            if (empty($pessoasTipos)) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de pessoa registrado.'];
            }

            return ['status' => 'success', 'message' => count($pessoasTipos) . ' tipo(s) de pessoa(s) encontrado(s)', 'dados' => $pessoasTipos];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um tipo de pessoa específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarPessoaTipo($coluna, $valor) {
        $colunasPermitidas = ['pessoa_tipo_id', 'pessoa_tipo_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas pessoa_tipo_id e pessoa_tipo_nome são permitidos.'];
        }

        try {
            $pessoaTipo = $this->pessoaTipoModel->buscar($coluna, $valor);
            if ($pessoaTipo) {
                return ['status' => 'success', 'dados' => $pessoaTipo];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de pessoa não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um tipo de pessoa.
     *
     * @param string $pessoa_tipo_id ID do tipo de pessoa a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarPessoaTipo($pessoa_tipo_id) {
        try {
            $pessoaTipo = $this->buscarPessoaTipo('pessoa_tipo_id', $pessoa_tipo_id);

            if ($pessoaTipo['status'] == 'not_found') {
                return $pessoaTipo;
            }

            if ($pessoaTipo['dados'][0]['pessoa_tipo_cliente'] == 1) {
                return ['status' => 'bad_request', 'message' => 'Essa operação não é permitida para o tipo de pessoa padrão.'];
            }

            $this->pessoaTipoModel->apagar($pessoa_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de pessoa apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o tipo de pessoa. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

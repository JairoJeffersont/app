<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\PostagemModel;
use PDOException;

/**
 * Classe PostagemController
 *
 * Controla as operações relacionadas às postagens, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class PostagemController
{

    /**
     * @var PostagemModel Instância do modelo PostagemModel para interagir com os dados.
     */
    private $postagemModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * Construtor do PostagemController.
     *
     * Inicializa as instâncias do modelo PostagemModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->postagemModel = new PostagemModel();
        $this->logger = new Logger();
    }

    /**
     * Método para criar uma nova postagem.
     *
     * @param array $dados Associativo com os dados da postagem a serem inseridos. Campos obrigatórios:
     *                     postagem_titulo, postagem_status, postagem_criada_por, postagem_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarPostagem($dados)
    {
        $camposObrigatorios = ['postagem_titulo', 'postagem_status', 'postagem_criada_por', 'postagem_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $dados['postagem_pasta'] = './public/arquivos/postagens/' . $dados['postagem_cliente'] . '/' . uniqid();

            if (!is_dir($dados['postagem_pasta'])) {
                mkdir($dados['postagem_pasta'], 0777, true);
            }

            $this->postagemModel->criar($dados);
            return ['status' => 'success', 'message' => 'Postagem criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Já existe uma postagem com este título.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de uma postagem existente.
     *
     * @param string $postagem_id ID da postagem a ser atualizado.
     * @param array $dados Associativo com os dados atualizados da postagem.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarPostagem($postagem_id, $dados)
    {
        $camposObrigatorios = ['postagem_titulo', 'postagem_status'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $postagem = $this->buscarPostagem('postagem_id', $postagem_id);

        if ($postagem['status'] == 'not_found') {
            return $postagem;
        }

        try {
            $this->postagemModel->atualizar($postagem_id, $dados);
            return ['status' => 'success', 'message' => 'Postagem atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todas as postagens registradas de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de postagens.
     */
    public function listarPostagens($itens, $pagina, $ordem, $ordenarPor, $ano, $cliente)
    {
        try {
            $postagens = $this->postagemModel->listar($itens, $pagina, $ordem, $ordenarPor, $ano, $cliente);

            $total = (isset($postagens[0]['total'])) ? $postagens[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($postagens)) {
                return ['status' => 'empty', 'message' => 'Nenhuma postagem registrada.'];
            }

            return ['status' => 'success', 'message' => count($postagens) . ' postagem(s) encontrada(s)', 'dados' => $postagens, 'total_paginas' => $totalPaginas,];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar uma postagem específica baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarPostagem($coluna, $valor)
    {
        $colunasPermitidas = ['postagem_id', 'postagem_titulo'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas postagem_id e postagem_titulo são permitidos.'];
        }

        try {
            $postagem = $this->postagemModel->buscar($coluna, $valor);
            if ($postagem) {
                return ['status' => 'success', 'dados' => $postagem];
            } else {
                return ['status' => 'not_found', 'message' => 'Postagem não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar uma postagem.
     *
     * @param string $postagem_id ID da postagem a ser apagada.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarPostagem($postagem_id)
    {
        try {
            $postagem = $this->buscarPostagem('postagem_id', $postagem_id);

            if ($postagem['status'] == 'not_found') {
                return $postagem;
            }

            $pasta = $postagem['dados'][0]['postagem_pasta'];

            if (is_dir($pasta)) {
                $files = array_diff(scandir($pasta), ['.', '..']);
                foreach ($files as $file) {
                    unlink($pasta . DIRECTORY_SEPARATOR . $file);
                }
                rmdir($pasta);
            }

            $this->postagemModel->apagar($postagem_id);
            return ['status' => 'success', 'message' => 'Postagem apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a postagem. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

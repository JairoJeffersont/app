<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\PessoaModel;
use GabineteDigital\Middleware\FileUploader;
use PDOException;

/**
 * Classe PessoaController
 *
 * Controla as operações relacionadas a pessoas, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class PessoaController
{

    /**
     * @var PessoaModel Instância do modelo PessoaModel para interagir com os dados.
     */
    private $pessoaModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;


    private $fileUploader;
    private $pasta_foto;


    /**
     * Construtor do PessoaController.
     *
     * Inicializa as instâncias do modelo PessoaModel e do Logger para gerenciamento de logs.
     */
    public function __construct()
    {
        $this->pessoaModel = new PessoaModel();
        $this->fileUploader = new FileUploader();
        $this->pasta_foto = 'public/arquivos/fotos_pessoas/';
        $this->logger = new Logger();
    }

    /**
     * Método para criar uma nova pessoa.
     *
     * @param array $dados Associativo com os dados da pessoa a serem inseridos. Campos obrigatórios:
     *                     pessoa_nome, pessoa_email, pessoa_telefone, pessoa_endereco, pessoa_criado_por.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarPessoa($dados)
    {
        $camposObrigatorios = ['pessoa_nome', 'pessoa_email', 'pessoa_aniversario', 'pessoa_municipio', 'pessoa_estado', 'pessoa_criada_por', 'pessoa_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }


        if (!empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto, $dados['foto'], ['jpg', 'jpeg', 'png'], 2);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            $dados['pessoa_foto'] = $uploadResult['file_path'];
        }

        try {
            $this->pessoaModel->criar($dados);
            return ['status' => 'success', 'message' => 'Pessoa criada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de uma pessoa existente.
     *
     * @param string $pessoa_id ID da pessoa a ser atualizada.
     * @param array $dados Associativo com os dados atualizados da pessoa.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarPessoa($pessoa_id, $dados)
    {
        $camposObrigatorios = ['pessoa_nome', 'pessoa_email', 'pessoa_aniversario', 'pessoa_municipio', 'pessoa_estado', 'pessoa_criada_por', 'pessoa_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $pessoa = $this->buscarPessoa('pessoa_id', $pessoa_id);

        if ($pessoa['status'] == 'not_found') {
            return $pessoa;
        }

        if (!empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto, $dados['foto'], ['jpg', 'jpeg', 'png'], 2);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            if (!empty($pessoa['dados'][0]['pessoa_foto'])) {
                $this->fileUploader->deleteFile($pessoa['dados'][0]['pessoa_foto']);
            }

            $dados['pessoa_foto'] = $uploadResult['file_path'];
        } else {
            $dados['pessoa_foto'] = $pessoa['dados'][0]['pessoa_foto'] ?? null;
        }


        try {
            $this->pessoaModel->atualizar($pessoa_id, $dados);
            return ['status' => 'success', 'message' => 'Pessoa atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar pessoas com filtros e paginação.
     *
     * @param int $itens Número de itens por página.
     * @param int $pagina Número da página.
     * @param string $ordem Ordem de classificação (ASC ou DESC).
     * @param string $ordenarPor Coluna para ordenação.
     * @param string|null $termo Termo de busca (opcional).
     * @param int $cliente ID do cliente associado.
     * @return array Retorna um array com o status da operação, mensagem e lista de pessoas.
     */
    public function listarPessoas($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente)
    {
        try {
            $result = $this->pessoaModel->listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente);

            $total = (isset($result[0]['total'])) ? $result[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhuma pessoa encontrada.'];
            }

            return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $result];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar uma pessoa específica baseada em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados ou mensagem de registro não encontrado.
     */
    public function buscarPessoa($coluna, $valor)
    {
        try {
            $pessoa = $this->pessoaModel->buscar($coluna, $valor);
            if ($pessoa) {
                return ['status' => 'success', 'dados' => $pessoa];
            } else {
                return ['status' => 'not_found', 'message' => 'Pessoa não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar uma pessoa.
     *
     * @param string $pessoa_id ID da pessoa a ser apagada.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarPessoa($pessoa_id)
    {
        try {
            $pessoa = $this->buscarPessoa('pessoa_id', $pessoa_id);

            if ($pessoa['status'] == 'not_found') {
                return $pessoa;
            }

            $this->pessoaModel->apagar($pessoa_id);
            return ['status' => 'success', 'message' => 'Pessoa apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a pessoa. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

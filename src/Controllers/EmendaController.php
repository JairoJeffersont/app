<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\EmendaModel;
use GabineteDigital\Middleware\FileUploader;
use GabineteDigital\Middleware\Logger;
use PDOException;

/**
 * Classe EmendaController
 *
 * Controla as operações relacionadas a emendas, incluindo criação, atualização, listagem,
 * busca e exclusão de emendas.
 */
class EmendaController {

    /**
     * @var EmendaModel Instância do modelo EmendaModel para interagir com os dados.
     */
    private $emendaModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * @var FileUploader Instância do FileUploader para upload de arquivos.
     */
    private $fileUploader;

    /**
     * @var PastaArquivos Pasta padrão de arquivos das emendas.
     */
    private $pasta_arquivos;


    /**
     * Construtor do EmendaController.
     *
     * Inicializa as instâncias do modelo EmendaModel e do Logger para gerenciamento de logs.
     */
    public function __construct() {
        $this->emendaModel = new EmendaModel();
        $this->logger = new Logger();
        $this->fileUploader = new FileUploader();
        $this->pasta_arquivos = 'public/arquivos/emendas';
    }

    /**
     * Método para criar uma nova emenda.
     *
     * @param array $dados Associativo com os dados da emenda a serem inseridos. Campos obrigatórios:
     *                     emenda_numero, emenda_valor, emenda_descricao, emenda_status, ementa_orgao,
     *                     emenda_municipio, emenda_objetivo, emenda_informacoes, emenda_tipo, emenda_cliente.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarEmenda($dados) {
        $camposObrigatorios = ['emenda_numero', 'emenda_valor', 'emenda_descricao', 'emenda_status', 'emenda_orgao', 
                               'emenda_municipio', 'emenda_estado',  'emenda_objetivo', 'emenda_informacoes', 'emenda_tipo', 'emenda_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        // Lógica adicional, se necessário, pode ser adicionada aqui...

        try {
            $this->emendaModel->criar($dados);
            return ['status' => 'success', 'message' => 'Emenda inserida com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de uma emenda existente.
     *
     * @param string $emenda_id ID da emenda a ser atualizada.
     * @param array $dados Associativo com os dados atualizados da emenda.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarEmenda($emenda_id, $dados) {
        $camposObrigatorios = ['emenda_numero', 'emenda_valor', 'emenda_descricao', 'emenda_status', 'emenda_orgao', 
                               'emenda_municipio', 'emenda_estado', 'emenda_objetivo', 'emenda_informacoes', 'emenda_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        // Lógica para verificar se a emenda existe
        $emenda = $this->buscarEmenda('emenda_id', $emenda_id);

        if ($emenda['status'] == 'not_found') {
            return $emenda;
        }

        try {
            $this->emendaModel->atualizar($emenda_id, $dados);
            return ['status' => 'success', 'message' => 'Emenda atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todas as emendas de um cliente.
     *
     * @param string $cliente ID do cliente.
     * @return array Retorna um array com o status da operação, mensagem e lista de emendas.
     */
    public function listarEmendas($itens, $pagina, $ordem, $ordenarPor, $status, $tipo, $objetivo, $ano, $cliente) {
        try {
            $emendas = $this->emendaModel->listar($itens, $pagina, $ordem, $ordenarPor, $status, $tipo, $objetivo, $ano, $cliente);


            $total = (isset($emendas[0]['total'])) ? $emendas[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($emendas)) {
                return ['status' => 'empty', 'message' => 'Nenhuma emenda registrada'];
            }

            return ['status' => 'success', 'message' => count($emendas) . ' emenda(s) encontrada(s)', 'dados' => $emendas, 'total_paginas' => $totalPaginas];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar uma emenda específica baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados da emenda ou mensagem de emenda não encontrada.
     */
    public function buscarEmenda($coluna, $valor) {
        $colunasPermitidas = ['emenda_id', 'emenda_numero', 'emenda_status'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas emenda_id e emenda_numero são permitidos.'];
        }

        try {
            $emenda = $this->emendaModel->buscar($coluna, $valor);
            if ($emenda) {
                return ['status' => 'success', 'dados' => $emenda];
            } else {
                return ['status' => 'not_found', 'message' => 'Emenda não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar uma emenda.
     *
     * @param string $emenda_id ID da emenda a ser apagada.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarEmenda($emenda_id) {
        try {
            $emenda = $this->buscarEmenda('emenda_id', $emenda_id);

            if ($emenda['status'] == 'not_found') {
                return $emenda;
            }

            $this->emendaModel->apagar($emenda_id);
            return ['status' => 'success', 'message' => 'Emenda apagada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

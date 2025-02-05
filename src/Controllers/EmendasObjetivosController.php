<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\EmendasObjetivosModel;
use PDOException;

/**
 * Classe EmendasObjetivosController
 *
 * Controla as operações relacionadas aos objetivos das emendas, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class EmendasObjetivosController {
    private $emendasObjetivosModel;
    private $logger;

    public function __construct() {
        $this->emendasObjetivosModel = new EmendasObjetivosModel();
        $this->logger = new Logger();
    }

    public function criarEmendasObjetivo($dados) {
        $camposObrigatorios = ['emendas_objetivos_nome', 'emendas_objetivos_criado_por', 'emendas_objetivos_cliente', 'emendas_objetivos_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->emendasObjetivosModel->criar($dados);
            return ['status' => 'success', 'message' => 'Objetivo de emenda criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do objetivo de emenda já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('emendas_objetivos_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function atualizarEmendasObjetivo($emendas_objetivos_id, $dados) {
        $camposObrigatorios = ['emendas_objetivos_nome'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $emendasObjetivo = $this->buscarEmendasObjetivo('emendas_objetivos_id', $emendas_objetivos_id);

        if ($emendasObjetivo['status'] == 'not_found') {
            return $emendasObjetivo;
        }

        try {
            $this->emendasObjetivosModel->atualizar($emendas_objetivos_id, $dados);
            return ['status' => 'success', 'message' => 'Objetivo de emenda atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emendas_objetivos_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarEmendasObjetivos($cliente) {
        try {
            $emendasObjetivos = $this->emendasObjetivosModel->listar($cliente);

            if (empty($emendasObjetivos)) {
                return ['status' => 'empty', 'message' => 'Nenhum objetivo de emenda registrado.'];
            }

            return ['status' => 'success', 'message' => count($emendasObjetivos) . ' objetivo(s) de emenda(s) encontrado(s)', 'dados' => $emendasObjetivos];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emendas_objetivos_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarEmendasObjetivo($coluna, $valor) {
        $colunasPermitidas = ['emendas_objetivos_id', 'emendas_objetivos_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas emendas_objetivos_id e emendas_objetivos_nome são permitidos.'];
        }

        try {
            $emendasObjetivo = $this->emendasObjetivosModel->buscar($coluna, $valor);
            if ($emendasObjetivo) {
                return ['status' => 'success', 'dados' => $emendasObjetivo];
            } else {
                return ['status' => 'not_found', 'message' => 'Objetivo de emenda não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emendas_objetivos_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarEmendasObjetivo($emendas_objetivos_id) {
        try {
            $emendasObjetivo = $this->buscarEmendasObjetivo('emendas_objetivos_id', $emendas_objetivos_id);

            if ($emendasObjetivo['status'] == 'not_found') {
                return $emendasObjetivo;
            }

            $this->emendasObjetivosModel->apagar($emendas_objetivos_id);
            return ['status' => 'success', 'message' => 'Objetivo de emenda apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o objetivo de emenda. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('emendas_objetivos_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

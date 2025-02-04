<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\EmendasStatusModel;
use PDOException;

/**
 * Classe EmendasStatusController
 *
 * Controla as operações relacionadas ao status das emendas, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class EmendasStatusController {
    private $emendasStatusModel;
    private $logger;

    public function __construct() {
        $this->emendasStatusModel = new EmendasStatusModel();
        $this->logger = new Logger();
    }

    public function criarEmendasStatus($dados) {
        $camposObrigatorios = ['emendas_status_nome', 'emendas_status_criado_por', 'emendas_status_cliente', 'emendas_status_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->emendasStatusModel->criar($dados);
            return ['status' => 'success', 'message' => 'Status de emenda criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O nome do status de emenda já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('emendas_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function atualizarEmendasStatus($emendas_status_id, $dados) {
        $camposObrigatorios = ['emendas_status_nome'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $emendasStatus = $this->buscarEmendasStatus('emendas_status_id', $emendas_status_id);

        if ($emendasStatus['status'] == 'not_found') {
            return $emendasStatus;
        }

        try {
            $this->emendasStatusModel->atualizar($emendas_status_id, $dados);
            return ['status' => 'success', 'message' => 'Status de emenda atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emendas_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarEmendasStatus($cliente) {
        try {
            $emendasStatus = $this->emendasStatusModel->listar($cliente);

            if (empty($emendasStatus)) {
                return ['status' => 'empty', 'message' => 'Nenhum status de emenda registrado.'];
            }

            return ['status' => 'success', 'message' => count($emendasStatus) . ' status(s) de emenda(s) encontrado(s)', 'dados' => $emendasStatus];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emendas_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarEmendasStatus($coluna, $valor) {
        $colunasPermitidas = ['emendas_status_id', 'emendas_status_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas emendas_status_id e emendas_status_nome são permitidos.'];
        }

        try {
            $emendasStatus = $this->emendasStatusModel->buscar($coluna, $valor);
            if ($emendasStatus) {
                return ['status' => 'success', 'dados' => $emendasStatus];
            } else {
                return ['status' => 'not_found', 'message' => 'Status de emenda não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emendas_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarEmendasStatus($emendas_status_id) {
        try {
            $emendasStatus = $this->buscarEmendasStatus('emendas_status_id', $emendas_status_id);

            if ($emendasStatus['status'] == 'not_found') {
                return $emendasStatus;
            }

            $this->emendasStatusModel->apagar($emendas_status_id);
            return ['status' => 'success', 'message' => 'Status de emenda apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar o status de emenda. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('emendas_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

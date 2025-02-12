<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;


use GabineteDigital\Models\ProposicaoModel;
use PDOException;


class ProposicaoController
{


    private $proposicaoModel;
    private $logger;


    public function __construct()
    {
        $this->proposicaoModel = new ProposicaoModel();
        $this->logger = new Logger();
    }

    public function buscarProposicoesGabinete($autor, $ano, $tipo, $itens, $pagina, $ordem, $ordenarPor, $arquivado)
    {
        try {
            $result = $this->proposicaoModel->buscarProposicoesGabinete($autor, $ano, $tipo, $itens, $pagina, $ordem, $ordenarPor, $arquivado);

            $total = (isset($result[0]['total'])) ? $result[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhuma proposição encontrada.'];
            }

            return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $result];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('proposicao_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}

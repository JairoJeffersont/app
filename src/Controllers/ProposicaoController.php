<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\GetJson;
use GabineteDigital\Middleware\Logger;


use GabineteDigital\Models\ProposicaoModel;
use PDOException;


class ProposicaoController
{


    private $proposicaoModel;
    private $logger;
    private $getJson;


    public function __construct()
    {
        $this->proposicaoModel = new ProposicaoModel();
        $this->logger = new Logger();
        $this->getJson = new GetJson();
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


    public function buscaProposicao($coluna, $valor)
    {
        $colunasPermitidas = ['proposicao_id', 'proposicao_autor_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas nota_id e nota_proposicao são permitidos.'];
        }

        try {
            $proposicao = $this->proposicaoModel->buscar($coluna, $valor);
            if ($proposicao) {
                return ['status' => 'success', 'dados' => $proposicao];
            } else {
                return ['status' => 'not_found', 'message' => 'Proposição não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('nota_tecnica_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarDetalhe($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId);
    }

    public function buscarTramitacoes($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId . '/tramitacoes');
    }

    public function buscarProposicoesSenado($autor, $ano, $tipo)
    {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/pesquisa/lista?sigla='.$tipo.'&ano='.$ano.'&nomeAutor='.$autor);
    }

    public function buscarDetalheSenado($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/'.$proposicaoId);
    }
}

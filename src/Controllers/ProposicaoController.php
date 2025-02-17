<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\GetJson;

class ProposicaoController
{

    private $getJson;

    public function __construct()
    {

        $this->getJson = new GetJson();
    }




    public function buscarProposicoesDeputado($autor, $ano, $itens, $pagina, $tipo)
    {
        $buscaDep = $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/deputados?nome=' . $autor . '&ordem=ASC&ordenarPor=nome');

        if ($buscaDep['status'] == 'success' && !empty($buscaDep['dados'])) {
            $idDeputado = $buscaDep['dados'][0]['id'];
        } else if ($buscaDep['status'] == 'success' && empty($buscaDep['dados'])) {
            return [
                'status' => 'not_found',
                'message' => 'Deputado não encontrado'
            ];
        } else {
            return $buscaDep;
        }

        $response = $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes?idDeputadoAutor=' . $idDeputado . '&itens=' . $itens . '&pagina=' . $pagina . '&ano='.$ano.'&ordem=DESC&ordenarPor=id&siglaTipo=' . $tipo);

        $proposicoes = $response['dados'];
        $total_registros = isset($response['headers']['x-total-count']) ? (int) $response['headers']['x-total-count'] : 0;
        $total_paginas = $itens > 0 ? ceil($total_registros / $itens) : 1;

        if (empty($proposicoes)) {
            return ['status' => 'empty', 'message' => 'Nenhuma proposição encontrada.'];
        }

        return [
            'status' => 'success',
            'dados' => $proposicoes,
            'total_paginas' => $total_paginas
        ];
    }



    public function buscarDetalhe($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId);
    }

    public function buscarTramitacoes($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId . '/tramitacoes');
    }

    public function buscarAutores($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $proposicaoId . '/autores');
    }

    public function buscarProposicoesSenado($autor, $ano, $tipo)
    {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/pesquisa/lista?sigla=' . $tipo . '&ano=' . $ano . '&nomeAutor=' . $autor);
    }

    public function buscarDetalheSenado($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/' . $proposicaoId);
    }

    public function buscarTextoSenado($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/textos/' . $proposicaoId);
    }

    public function buscarTramitacoesSenado($proposicaoId)
    {
        return $this->getJson->pegarDadosURL('https://legis.senado.leg.br/dadosabertos/materia/movimentacoes/' . $proposicaoId);
    }
}

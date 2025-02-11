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

    public function listarProposicoesCD($ano, $autor, $tipo,  $itens, $pagina)
    {
        $idAutor = $this->buscarIdDepCD($autor)[0]['id'];

        $buscaProposicoes = $this->getJson->getJson("https://dadosabertos.camara.leg.br/api/v2/proposicoes?ano=$ano&siglaTipo=$tipo&idDeputadoAutor=$idAutor&itens=$itens&pagina=$pagina&ordem=DESC&ordenarPor=id", ['Content-Type: application/json']);

        $proposicoesRetorno = [];
        $totalRegistros = isset($buscaProposicoes['headers']['x-total-count']) ? $buscaProposicoes['headers']['x-total-count'] : 0;
        $totalPaginas = ceil($totalRegistros / $itens);

        if (isset($buscaProposicoes['dados']['dados'])) {
            foreach ($buscaProposicoes['dados']['dados'] as $proposicao) {
                $autores = $this->buscarAutoresCD($proposicao['id']);
                $detalhes = $this->buscarDetalhesCD($proposicao['id']);

                $proposicoesRetorno[] = [
                    'proposicao_id' => $proposicao['id'],
                    'proposicao_titulo' => $proposicao['siglaTipo'] . ' ' . $proposicao['numero'] . '/' . $proposicao['ano'],
                    'proposicao_tipo' => $proposicao['siglaTipo'],
                    'proposicao_numero' => $proposicao['numero'],
                    'proposicao_ano' => $proposicao['ano'],
                    'proposicao_ementa' => htmlspecialchars($proposicao['ementa']),
                    'proposicao_apresentacao' => $detalhes['dataApresentacao'],
                    'proposicao_documento' => $detalhes['urlInteiroTeor'],
                    'proposicao_autores' => $autores
                ];
            }
        }

        return [
            'dados' => $proposicoesRetorno,
            'total_paginas' => $totalPaginas
        ];
    }


    public function buscarAutoresCD($id)
    {
        $buscaAutores = $this->getJson->getJson("https://dadosabertos.camara.leg.br/api/v2/proposicoes/$id/autores", ['Content-Type: application/json']);
        return $buscaAutores['dados']['dados'];
    }

    public function buscarDetalhesCD($id)
    {
        $buscaAutores = $this->getJson->getJson("https://dadosabertos.camara.leg.br/api/v2/proposicoes/$id", ['Content-Type: application/json']);
        return $buscaAutores['dados']['dados'];
    }

    public function buscarIdDepCD($nomeDep)
    {
        $buscaAutores = $this->getJson->getJson("https://dadosabertos.camara.leg.br/api/v2/deputados?nome=$nomeDep&ordem=ASC&ordenarPor=nome", ['Content-Type: application/json']);
        return $buscaAutores['dados']['dados'];
    }
}

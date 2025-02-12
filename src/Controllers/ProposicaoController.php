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

    public function listarProposicoesDeputadoCD($ano, $autor, $tipo, $itens, $pagina, $ordem, $ordenarPor)
    {
        $idDep = $this->buscarIdDep($autor);

        $buscaProposicoes = $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes?siglaTipo=' . $tipo . '&ano=' . $ano . '&idDeputadoAutor=' . $idDep . '&itens=' . $itens . '&pagina=' . $pagina . '&ordem=' . $ordem . '&ordenarPor=' . $ordenarPor);

        $total_paginas = ceil($buscaProposicoes['headers']['x-total-count'] / $itens);

        if ($buscaProposicoes['status'] == 'success' && !empty($buscaProposicoes['dados'])) {
            foreach ($buscaProposicoes['dados'] as $proposicao) {
                $dadosRertorno[] = [
                    'proposicao_id' => $proposicao['id'],
                    'proposicao_titulo' => $proposicao['siglaTipo'] . ' ' . $proposicao['numero'] . '/' . $proposicao['ano'],
                    'proposicao_numero' => $proposicao['numero'],
                    'proposicao_tipo' => $proposicao['siglaTipo'],
                    'proposicao_ano' => $proposicao['ano'],
                    'proposicao_ementa' => htmlspecialchars($proposicao['ementa']),
                    'proposicao_autores' => $this->buscarAutores($proposicao['id'])
                ];
            }
            return ["status" => 'success', "dados" => $dadosRertorno, 'total_paginas' => $total_paginas];
        } else if (isset($buscaProposicoes['dados']) && empty($buscaProposicoes['dados'])) {
            return ["status" => "empty", "message" => "Nenhuma proposição encontrada"];
        } else if ($buscaProposicoes['status'] == 'error') {
            return $buscaProposicoes;
        }
    }


    public function buscarAutores($id)
    {
        $buscaAutores =  $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes/' . $id . '/autores');
        if ($buscaAutores['status'] == 'success' && !empty($buscaAutores['dados'])) {

            foreach ($buscaAutores['dados'] as $autor) {
                $dadosRetorno[] = [
                    'autor_nome' => $autor['nome'],
                    'autor_assinatura' => $autor['ordemAssinatura'],
                    'autor_proponente' => $autor['proponente']
                ];
            }

            return $dadosRetorno;
        } else if ($buscaAutores['status'] == 'error') {
            return $buscaAutores;
        } else if (isset($buscaProposicoes['dados']) && empty($buscaProposicoes['dados'])) {
            return ["status" => "empty", "message" => "Nenhum autor encontrado"];
        }
    }


    public function buscarIdDep($nome)
    {
        $buscaId = $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/deputados?nome=' . $nome . '&ordem=ASC&ordenarPor=nome');



        if ($buscaId['status'] == 'success') {
            return $buscaId['dados'][0]['id'];
        } else if ($buscaId['status'] == 'error') {
            return $buscaId;
        }
    }
}

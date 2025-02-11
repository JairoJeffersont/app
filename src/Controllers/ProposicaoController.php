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

    public function atualizar($ano, $autor, $tipo, $itens, $pagina)
    {
        $buscaProposicoes = $this->getJson->pegarDadosURL('https://dadosabertos.camara.leg.br/api/v2/proposicoes?siglaTipo=' . $tipo . '&ano=' . $ano . '&idDeputadoAutor=' . $autor . '&itens=' . $itens . '&pagina=' . $pagina . '&ordem=ASC&ordenarPor=id');

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
            return ["status" => 'success', "dados" => $dadosRertorno];
        } else if (isset($buscaProposicoes['dados']) && empty($buscaProposicoes['dados'])) {
            return ["status" => "empty", "message" => "Nenhuma proposição encontrada"];
        } else if ($buscaProposicoes['status'] == 'error') {
            return $buscaProposicoes;
        }

        return true;
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
}

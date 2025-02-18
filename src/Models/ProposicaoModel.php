<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;


class ProposicaoModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }


    public function criar($dados)
    {


        $query = "INSERT INTO proposicoes (
                    proposicao_id, proposicao_numero, proposicao_titulo, 
                    proposicao_ano, proposicao_tipo, proposicao_ementa, 
                    proposicao_apresentacao, proposicao_arquivada, 
                    proposicao_aprovada, proposicao_autor, 
                    proposicao_criada_por, proposicao_cliente
                ) VALUES (
                    :proposicao_id, :proposicao_numero, :proposicao_titulo, 
                    :proposicao_ano, :proposicao_tipo, :proposicao_ementa, 
                    :proposicao_apresentacao, :proposicao_arquivada, 
                    :proposicao_aprovada, :proposicao_autor, 
                    :proposicao_criada_por, :proposicao_cliente
                )";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':proposicao_id', $dados['proposicao_id'], PDO::PARAM_INT);

        $stmt->bindParam(':proposicao_numero', $dados['proposicao_numero'], PDO::PARAM_INT);
        $stmt->bindParam(':proposicao_titulo', $dados['proposicao_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_ano', $dados['proposicao_ano'], PDO::PARAM_INT);
        $stmt->bindParam(':proposicao_tipo', $dados['proposicao_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_ementa', $dados['proposicao_ementa'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_apresentacao', $dados['proposicao_apresentacao'], PDO::PARAM_STR); // Certifique-se que está no formato DATETIME
        $stmt->bindParam(':proposicao_arquivada', $dados['proposicao_arquivada'], PDO::PARAM_BOOL);
        $stmt->bindParam(':proposicao_aprovada', $dados['proposicao_aprovada'], PDO::PARAM_BOOL);
        $stmt->bindParam(':proposicao_autor', $dados['proposicao_autor'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_criada_por', $dados['proposicao_criada_por'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_cliente', $dados['proposicao_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }


    public function inserirTramitacao($proposicaoId, $tramitacaoTipo)
    {

        $query = "INSERT INTO tramitacoes (tramitacao_id, tramitacao_proposicao, tramitacao_tipo) VALUES (UUID(), :tramitacao_proposicao, :tramitacao_tipo)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':tramitacao_proposicao', $proposicaoId, PDO::PARAM_INT);
        $stmt->bindParam(':tramitacao_tipo', $tramitacaoTipo, PDO::PARAM_STR);

        return $stmt->execute();
    }


    public function listarProposicoesDB($autor, $itens, $pagina, $tipo, $ano)
    {
        $pagina = (int) $pagina;
        $itens = (int) $itens;
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT proposicoes.*, 
                         (SELECT COUNT(*) 
                          FROM proposicoes 
                          WHERE proposicao_autor = :autor 
                            AND proposicao_ano = :ano 
                            AND proposicao_tipo = :tipo) AS total 
                  FROM proposicoes 
                  WHERE proposicao_autor = :autor 
                    AND proposicao_ano = :ano 
                    AND proposicao_tipo = :tipo 
                  ORDER BY proposicao_id DESC 
                  LIMIT :offset, :itens";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':autor', $autor, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':ano', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM proposicoes WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarTramitacoesDB($coluna, $valor)
    {
        $query = "SELECT tramitacoes.*, proposicoes_tramitacoes.* FROM tramitacoes INNER JOIN proposicoes_tramitacoes ON tramitacoes.tramitacao_tipo = proposicoes_tramitacoes.proposicao_tramitacao_id WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

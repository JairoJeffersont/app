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


    public function buscarProposicoesGabinete($autor, $ano, $tipo, $itens, $pagina, $ordem, $ordenarPor, $arquivado)
    {
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        if ($arquivado) {
            $query = "SELECT view_proposicoes.*, (SELECT COUNT(*) FROM view_proposicoes WHERE proposicao_autor_nome = :autor AND proposicao_ano = :ano AND proposicao_tipo = :tipo AND proposicao_autor_proponente = 1 AND proposicao_autor_assinatura = 1 AND proposicao_arquivada = 1) AS total FROM view_proposicoes WHERE proposicao_autor_nome = :autor AND proposicao_ano = :ano AND proposicao_tipo = :tipo AND proposicao_autor_proponente = 1 AND proposicao_autor_assinatura = 1 AND proposicao_arquivada = 1 ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
        } else {
            $query = "SELECT view_proposicoes.*, (SELECT COUNT(*) FROM view_proposicoes WHERE proposicao_autor_nome = :autor AND proposicao_ano = :ano AND proposicao_tipo = :tipo AND proposicao_autor_proponente = 1 AND proposicao_autor_assinatura = 1 AND proposicao_arquivada = 0) AS total FROM view_proposicoes WHERE proposicao_autor_nome = :autor AND proposicao_ano = :ano AND proposicao_tipo = :tipo AND proposicao_autor_proponente = 1 AND proposicao_autor_assinatura = 1 AND proposicao_arquivada = 0 ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':autor', $autor, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_proposicoes WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

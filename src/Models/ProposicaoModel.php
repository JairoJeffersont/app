<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;


class ProposicaoModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function listarProposicoes($itens, $pagina, $tipo, $autor, $ordem, $ordenarPor, $ano, $termo, $arquivada) {
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        if (empty($termo)) {
            $query = "SELECT view_proposicoes.*, (SELECT COUNT(*) AS total FROM view_proposicoes WHERE proposicao_arquivada = :arquivada AND proposicao_ano = :ano AND proposicao_tipo = :tipo AND proposicao_autor_nome LIKE :autor AND proposicao_autor_proponente = 1 AND proposicao_autor_assinatura = 1) AS total FROM view_proposicoes WHERE proposicao_arquivada = :arquivada AND proposicao_ano = :ano AND proposicao_tipo = :tipo AND proposicao_autor_nome LIKE :autor AND proposicao_autor_proponente = 1 AND proposicao_autor_assinatura = 1 ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
        } else {
            $query = "SELECT view_proposicoes.*, (SELECT COUNT(*) AS total FROM view_proposicoes WHERE proposicao_arquivada = :arquivada AND (proposicao_titulo LIKE :termo OR proposicao_ementa LIKE :termo) AND proposicao_tipo = :tipo AND proposicao_autor_nome LIKE :autor AND proposicao_autor_proponente = 1 AND proposicao_autor_assinatura = 1) AS total FROM view_proposicoes WHERE proposicao_arquivada = :arquivada AND (proposicao_titulo LIKE :termo OR proposicao_ementa LIKE :termo) AND proposicao_tipo = :tipo AND proposicao_autor_nome LIKE :autor AND proposicao_autor_proponente = 1 AND proposicao_autor_assinatura = 1 ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':autor', $autor, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':arquivada', $arquivada, PDO::PARAM_INT);

        if (!empty($termo)) {
            $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

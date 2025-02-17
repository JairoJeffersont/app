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

    public function limpar($data)
    {
        $deleteQuery = "DELETE FROM proposicoes WHERE DATE(proposicao_apresentacao) = DATE(:proposicao_apresentacao)";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->bindValue(':proposicao_apresentacao', $data, PDO::PARAM_STR);
        $deleteStmt->execute();
    }


    public function criar($dados)
    {

        $query = "INSERT INTO proposicoes (
                    proposicao_id, proposicao_numero, proposicao_titulo, 
                    proposicao_ano, proposicao_tipo, proposicao_ementa, 
                    proposicao_apresentacao, proposicao_arquivada, 
                    proposicao_aprovada, proposicao_principal
                ) VALUES (
                    :proposicao_id, :proposicao_numero, :proposicao_titulo, 
                    :proposicao_ano, :proposicao_tipo, :proposicao_ementa, 
                    :proposicao_apresentacao, :proposicao_arquivada, 
                    :proposicao_aprovada, :proposicao_principal
                )";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':proposicao_id', $dados['proposicao_id'], PDO::PARAM_INT);
        $stmt->bindParam(':proposicao_numero', $dados['proposicao_numero'], PDO::PARAM_INT);
        $stmt->bindParam(':proposicao_titulo', $dados['proposicao_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_ano', $dados['proposicao_ano'], PDO::PARAM_INT);
        $stmt->bindParam(':proposicao_tipo', $dados['proposicao_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_ementa', $dados['proposicao_ementa'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_apresentacao', $dados['proposicao_apresentacao'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_arquivada', $dados['proposicao_arquivada'], PDO::PARAM_BOOL);
        $stmt->bindParam(':proposicao_aprovada', $dados['proposicao_aprovada'], PDO::PARAM_BOOL);
        $stmt->bindParam(':proposicao_principal', $dados['proposicao_principal'], PDO::PARAM_INT);

        return $stmt->execute();
    }
}

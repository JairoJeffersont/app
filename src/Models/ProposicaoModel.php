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
                    UUID(), :proposicao_numero, :proposicao_titulo, 
                    :proposicao_ano, :proposicao_tipo, :proposicao_ementa, 
                    :proposicao_apresentacao, :proposicao_arquivada, 
                    :proposicao_aprovada, :proposicao_autor, 
                    :proposicao_criada_por, :proposicao_cliente
                )";

        $stmt = $this->conn->prepare($query);

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

    
}

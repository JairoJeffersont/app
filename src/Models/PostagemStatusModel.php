<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe PostagemStatusModel
 * 
 * Representa a model para operações na tabela `postagem_status`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class PostagemStatusModel
{

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe PostagemStatusModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo status de postagem.
     * 
     * @param array $dados Dados do status de postagem (postagem_status_nome, postagem_status_descricao, postagem_status_criado_por, postagem_status_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO postagem_status (postagem_status_nome, postagem_status_descricao, postagem_status_criado_por, postagem_status_cliente)
                  VALUES (:postagem_status_nome, :postagem_status_descricao, :postagem_status_criado_por, :postagem_status_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':postagem_status_nome', $dados['postagem_status_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status_descricao', $dados['postagem_status_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status_criado_por', $dados['postagem_status_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status_cliente', $dados['postagem_status_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um status de postagem.
     * 
     * @param string $postagem_status_id ID do status de postagem a ser atualizado.
     * @param array $dados Novos dados do status de postagem.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($postagem_status_id, $dados)
    {
        $query = "UPDATE postagem_status SET postagem_status_nome = :postagem_status_nome, postagem_status_descricao = :postagem_status_descricao
                  WHERE postagem_status_id = :postagem_status_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':postagem_status_nome', $dados['postagem_status_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status_descricao', $dados['postagem_status_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status_id', $postagem_status_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os status de postagens.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os status de postagens.
     */
    public function listar($cliente)
    {
        $query = "SELECT * FROM view_postagens_status  WHERE postagem_status_cliente = :cliente OR postagem_status_cliente = 1 ORDER BY postagem_status_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar status de postagens por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_postagens_status  WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um status de postagem pelo seu ID.
     * 
     * @param string $postagem_status_id ID do status de postagem a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($postagem_status_id)
    {
        $query = "DELETE FROM postagem_status WHERE postagem_status_id = :postagem_status_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':postagem_status_id', $postagem_status_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe PostagemModel
 * 
 * Representa a model para operações na tabela `postagens`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class PostagemModel
{

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe PostagemModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar uma nova postagem.
     * 
     * @param array $dados Dados da postagem (postagem_titulo, postagem_data, postagem_pasta, postagem_informacoes, postagem_midias, postagem_status, postagem_criada_por, postagem_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO postagens (postagem_titulo, postagem_data, postagem_pasta, postagem_informacoes, postagem_midias, postagem_status, postagem_criada_por, postagem_cliente)
                  VALUES (:postagem_titulo, :postagem_data, :postagem_pasta, :postagem_informacoes, :postagem_midias, :postagem_status, :postagem_criada_por, :postagem_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':postagem_titulo', $dados['postagem_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_data', $dados['postagem_data'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_pasta', $dados['postagem_pasta'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_informacoes', $dados['postagem_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_midias', $dados['postagem_midias'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status', $dados['postagem_status'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_criada_por', $dados['postagem_criada_por'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_cliente', $dados['postagem_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de uma postagem.
     * 
     * @param string $postagem_id ID da postagem a ser atualizado.
     * @param array $dados Novos dados da postagem.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($postagem_id, $dados)
    {
        $query = "UPDATE postagens SET postagem_titulo = :postagem_titulo, postagem_data = :postagem_data, postagem_pasta = :postagem_pasta, 
                  postagem_informacoes = :postagem_informacoes, postagem_midias = :postagem_midias, postagem_status = :postagem_status
                  WHERE postagem_id = :postagem_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':postagem_titulo', $dados['postagem_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_data', $dados['postagem_data'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_pasta', $dados['postagem_pasta'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_informacoes', $dados['postagem_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_midias', $dados['postagem_midias'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status', $dados['postagem_status'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_id', $postagem_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todas as postagens de um cliente.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todas as postagens do cliente.
     */
    public function listar($cliente)
    {
        $query = "SELECT * FROM view_postagens WHERE postagem_cliente = :cliente ORDER BY postagem_titulo ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar postagens por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_postagens WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar uma postagem pelo seu ID.
     * 
     * @param string $postagem_id ID da postagem a ser deletada.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($postagem_id)
    {
        $query = "DELETE FROM postagens WHERE postagem_id = :postagem_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':postagem_id', $postagem_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

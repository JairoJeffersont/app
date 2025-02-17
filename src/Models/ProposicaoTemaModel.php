<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe ProposicaoTemaModel
 * 
 * Representa a model para operações na tabela `proposicao_tema`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class ProposicaoTemaModel
{

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe ProposicaoTemaModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo proposição de tema.
     * 
     * @param array $dados Dados do proposição de tema (proposicao_tema_nome, proposicao_tema_criado_por, proposicao_tema_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO proposicao_tema (proposicao_tema_id, proposicao_tema_nome, proposicao_tema_criado_por, proposicao_tema_cliente)
                  VALUES (UUID(), :proposicao_tema_nome, :proposicao_tema_criado_por, :proposicao_tema_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':proposicao_tema_nome', $dados['proposicao_tema_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tema_criado_por', $dados['proposicao_tema_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tema_cliente', $dados['proposicao_tema_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um proposição de tema.
     * 
     * @param string $proposicao_tema_id ID do proposição de tema a ser atualizado.
     * @param array $dados Novos dados do proposição de tema.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($proposicao_tema_id, $dados)
    {
        $query = "UPDATE proposicao_tema SET proposicao_tema_nome = :proposicao_tema_nome
                  WHERE proposicao_tema_id = :proposicao_tema_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':proposicao_tema_nome', $dados['proposicao_tema_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tema_id', $proposicao_tema_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os proposições de temas.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os proposições de temas.
     */
    public function listar($cliente)
    {
        $query = "SELECT * FROM view_proposicao_tema WHERE proposicao_tema_cliente = :cliente OR proposicao_tema_cliente = 1 ORDER BY proposicao_tema_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar proposições de temas por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_proposicao_tema WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um proposição de tema pelo seu ID.
     * 
     * @param string $proposicao_tema_id ID do proposição de tema a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($proposicao_tema_id)
    {
        $query = "DELETE FROM proposicao_tema WHERE proposicao_tema_id = :proposicao_tema_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':proposicao_tema_id', $proposicao_tema_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

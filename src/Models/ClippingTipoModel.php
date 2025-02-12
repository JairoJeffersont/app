<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe ClippingTipoModel
 * 
 * Representa a model para operações na tabela `clipping_tipos`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class ClippingTipoModel
{

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe ClippingTipoModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo tipo de clipping.
     * 
     * @param array $dados Dados do tipo de clipping (clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO clipping_tipos (clipping_tipo_id, clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_cliente)
                  VALUES (UUID(), :clipping_tipo_nome, :clipping_tipo_descricao, :clipping_tipo_criado_por, :clipping_tipo_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':clipping_tipo_nome', $dados['clipping_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo_descricao', $dados['clipping_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo_criado_por', $dados['clipping_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo_cliente', $dados['clipping_tipo_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um tipo de clipping.
     * 
     * @param string $clipping_tipo_id ID do tipo de clipping a ser atualizado.
     * @param array $dados Novos dados do tipo de clipping.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($clipping_tipo_id, $dados)
    {
        $query = "UPDATE clipping_tipos SET clipping_tipo_nome = :clipping_tipo_nome, clipping_tipo_descricao = :clipping_tipo_descricao
                  WHERE clipping_tipo_id = :clipping_tipo_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':clipping_tipo_nome', $dados['clipping_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo_descricao', $dados['clipping_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo_id', $clipping_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os tipos de clippings.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os tipos de clippings.
     */
    public function listar($cliente)
    {
        $query = "SELECT * FROM view_tipo_clipping WHERE clipping_tipo_cliente = :cliente OR clipping_tipo_cliente = '1' ORDER BY clipping_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar tipos de clippings por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_tipo_clipping WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um tipo de clipping pelo seu ID.
     * 
     * @param string $clipping_tipo_id ID do tipo de clipping a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($clipping_tipo_id)
    {
        $query = "DELETE FROM clipping_tipos WHERE clipping_tipo_id = :clipping_tipo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clipping_tipo_id', $clipping_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

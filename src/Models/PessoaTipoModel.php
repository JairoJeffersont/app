<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe PessoaTipoModel
 * 
 * Representa a model para operações na tabela `pessoas_tipos`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class PessoaTipoModel {

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe PessoaTipoModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo tipo de pessoa.
     * 
     * @param array $dados Dados do tipo de pessoa (pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO pessoas_tipos (pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente)
                  VALUES (:pessoa_tipo_nome, :pessoa_tipo_descricao, :pessoa_tipo_criado_por, :pessoa_tipo_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pessoa_tipo_nome', $dados['pessoa_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_tipo_descricao', $dados['pessoa_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_tipo_criado_por', $dados['pessoa_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_tipo_cliente', $dados['pessoa_tipo_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um tipo de pessoa.
     * 
     * @param string $pessoa_tipo_id ID do tipo de pessoa a ser atualizado.
     * @param array $dados Novos dados do tipo de pessoa.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($pessoa_tipo_id, $dados) {
        $query = "UPDATE pessoas_tipos SET pessoa_tipo_nome = :pessoa_tipo_nome, pessoa_tipo_descricao = :pessoa_tipo_descricao
                  WHERE pessoa_tipo_id = :pessoa_tipo_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pessoa_tipo_nome', $dados['pessoa_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_tipo_descricao', $dados['pessoa_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_tipo_id', $pessoa_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os tipos de pessoas.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os tipos de pessoas.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM view_pessoas_tipos WHERE pessoa_tipo_cliente = :cliente ORDER BY pessoa_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar tipos de pessoas por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_pessoas_tipos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um tipo de pessoa pelo seu ID.
     * 
     * @param string $pessoa_tipo_id ID do tipo de pessoa a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($pessoa_tipo_id) {
        $query = "DELETE FROM pessoas_tipos WHERE pessoa_tipo_id = :pessoa_tipo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pessoa_tipo_id', $pessoa_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}


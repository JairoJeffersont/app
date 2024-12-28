<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe PessoaProfissaoModel
 * 
 * Representa a model para operações na tabela `pessoas_profissoes`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class PessoaProfissaoModel {

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe PessoaProfissaoModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar uma nova profissão para pessoas.
     * 
     * @param array $dados Dados da profissão (pessoas_profissoes_nome, pessoas_profissoes_descricao, pessoas_profissoes_criado_por, pessoas_profissoes_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO pessoas_profissoes (pessoas_profissoes_nome, pessoas_profissoes_descricao, pessoas_profissoes_criado_por, pessoas_profissoes_cliente)
                  VALUES (:pessoas_profissoes_nome, :pessoas_profissoes_descricao, :pessoas_profissoes_criado_por, :pessoas_profissoes_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pessoas_profissoes_nome', $dados['pessoas_profissoes_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoas_profissoes_descricao', $dados['pessoas_profissoes_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoas_profissoes_criado_por', $dados['pessoas_profissoes_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoas_profissoes_cliente', $dados['pessoas_profissoes_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de uma profissão.
     * 
     * @param string $pessoas_profissoes_id ID da profissão a ser atualizada.
     * @param array $dados Novos dados da profissão.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($pessoas_profissoes_id, $dados) {
        $query = "UPDATE pessoas_profissoes SET pessoas_profissoes_nome = :pessoas_profissoes_nome, pessoas_profissoes_descricao = :pessoas_profissoes_descricao
                  WHERE pessoas_profissoes_id = :pessoas_profissoes_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pessoas_profissoes_nome', $dados['pessoas_profissoes_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoas_profissoes_descricao', $dados['pessoas_profissoes_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoas_profissoes_id', $pessoas_profissoes_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todas as profissões associadas a pessoas.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todas as profissões.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM view_pessoas_profissoes WHERE pessoas_profissoes_cliente = :cliente ORDER BY pessoas_profissoes_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar profissões por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_pessoas_profissoes WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar uma profissão pelo seu ID.
     * 
     * @param string $pessoas_profissoes_id ID da profissão a ser deletada.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($pessoas_profissoes_id) {
        $query = "DELETE FROM pessoas_profissoes WHERE pessoas_profissoes_id = :pessoas_profissoes_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pessoas_profissoes_id', $pessoas_profissoes_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

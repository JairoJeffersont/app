<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe EmendasStatusModel
 * 
 * Representa a model para operações na tabela `emendas_status`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class EmendasStatusModel {
    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe EmendasStatusModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo status de emenda.
     * 
     * @param array $dados Dados do status de emenda.
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO emendas_status (emendas_status_nome, emendas_status_descricao, emendas_status_criado_por, emendas_status_cliente)
                  VALUES (:emendas_status_nome, :emendas_status_descricao, :emendas_status_criado_por, :emendas_status_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':emendas_status_nome', $dados['emendas_status_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_status_descricao', $dados['emendas_status_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_status_criado_por', $dados['emendas_status_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_status_cliente', $dados['emendas_status_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um status de emenda.
     * 
     * @param string $emendas_status_id ID do status de emenda a ser atualizado.
     * @param array $dados Novos dados do status de emenda.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($emendas_status_id, $dados) {
        $query = "UPDATE emendas_status SET emendas_status_nome = :emendas_status_nome, emendas_status_descricao = :emendas_status_descricao
                  WHERE emendas_status_id = :emendas_status_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':emendas_status_nome', $dados['emendas_status_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_status_descricao', $dados['emendas_status_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_status_id', $emendas_status_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os status de emendas.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os status de emendas.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM view_emendas_status WHERE emendas_status_cliente = :cliente OR emendas_status_cliente = 1 ORDER BY emendas_status_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar status de emendas por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM emendas_status WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um status de emenda pelo seu ID.
     * 
     * @param string $emendas_status_id ID do status de emenda a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($emendas_status_id) {
        $query = "DELETE FROM emendas_status WHERE emendas_status_id = :emendas_status_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':emendas_status_id', $emendas_status_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

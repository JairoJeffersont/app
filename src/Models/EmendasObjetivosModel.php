<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe EmendasObjetivosModel
 * 
 * Representa a model para operações na tabela `emendas_objetivos`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class EmendasObjetivosModel {
    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe EmendasObjetivosModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo objetivo de emenda.
     * 
     * @param array $dados Dados do objetivo de emenda.
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO emendas_objetivos (emendas_objetivos_nome, emendas_objetivos_descricao, emendas_objetivos_criado_por, emendas_objetivos_cliente)
                  VALUES (:emendas_objetivos_nome, :emendas_objetivos_descricao, :emendas_objetivos_criado_por, :emendas_objetivos_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':emendas_objetivos_nome', $dados['emendas_objetivos_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_objetivos_descricao', $dados['emendas_objetivos_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_objetivos_criado_por', $dados['emendas_objetivos_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_objetivos_cliente', $dados['emendas_objetivos_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um objetivo de emenda.
     * 
     * @param string $emendas_objetivos_id ID do objetivo de emenda a ser atualizado.
     * @param array $dados Novos dados do objetivo de emenda.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($emendas_objetivos_id, $dados) {
        $query = "UPDATE emendas_objetivos SET emendas_objetivos_nome = :emendas_objetivos_nome, emendas_objetivos_descricao = :emendas_objetivos_descricao
                  WHERE emendas_objetivos_id = :emendas_objetivos_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':emendas_objetivos_nome', $dados['emendas_objetivos_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_objetivos_descricao', $dados['emendas_objetivos_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':emendas_objetivos_id', $emendas_objetivos_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os objetivos de emendas de um cliente.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os objetivos de emendas.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM view_emendas_objetivos WHERE emendas_objetivos_cliente = :cliente OR emendas_objetivos_cliente = 1 ORDER BY emendas_objetivos_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar um objetivo de emenda por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_emendas_objetivos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um objetivo de emenda pelo seu ID.
     * 
     * @param string $emendas_objetivos_id ID do objetivo de emenda a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($emendas_objetivos_id) {
        $query = "DELETE FROM emendas_objetivos WHERE emendas_objetivos_id = :emendas_objetivos_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':emendas_objetivos_id', $emendas_objetivos_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

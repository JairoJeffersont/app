<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe EmendaModel
 * 
 * Representa a model para operações na tabela `emendas`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class EmendaModel {

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe EmendaModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar uma nova emenda.
     * 
     * @param array $dados Dados da emenda.
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO emendas (emenda_numero, emenda_valor, emenda_descricao, emenda_status, emenda_orgao, emenda_municipio, emenda_estado, emenda_objetivo, emenda_informacoes, emenda_tipo, emenda_cliente, emenda_criado_por)
                  VALUES (:emenda_numero, :emenda_valor, :emenda_descricao, :emenda_status, :emenda_orgao, :emenda_municipio, :emenda_estado, :emenda_objetivo, :emenda_informacoes, :emenda_tipo, :emenda_cliente, :emenda_criado_por)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':emenda_numero', $dados['emenda_numero'], PDO::PARAM_INT);
        $stmt->bindParam(':emenda_valor', $dados['emenda_valor'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_descricao', $dados['emenda_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_status', $dados['emenda_status'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_orgao', $dados['emenda_orgao'], PDO::PARAM_STR); // Corrigido aqui
        $stmt->bindParam(':emenda_municipio', $dados['emenda_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_estado', $dados['emenda_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_objetivo', $dados['emenda_objetivo'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_informacoes', $dados['emenda_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_tipo', $dados['emenda_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_cliente', $dados['emenda_cliente'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_criado_por', $dados['emenda_criado_por'], PDO::PARAM_STR);


        return $stmt->execute();
    }

    /**
     * Método para listar todas as emendas de um cliente.
     * 
     * @param string $cliente ID do cliente.
     * @return array Retorna um array associativo com os dados das emendas.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM view_emendas WHERE emenda_cliente = :cliente ORDER BY emenda_numero ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar emendas por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_emendas WHERE $coluna = :valor";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para atualizar os dados de uma emenda.
     * 
     * @param string $emenda_id ID da emenda a ser atualizada.
     * @param array $dados Novos dados da emenda.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($emenda_id, $dados) {
        $query = "UPDATE emendas SET emenda_numero = :emenda_numero, emenda_estado = :emenda_estado, emenda_valor = :emenda_valor, emenda_descricao = :emenda_descricao, 
                  emenda_status = :emenda_status, emenda_orgao = :emenda_orgao, emenda_municipio = :emenda_municipio, 
                  emenda_objetivo = :emenda_objetivo, emenda_informacoes = :emenda_informacoes, emenda_tipo = :emenda_tipo
                  WHERE emenda_id = :emenda_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':emenda_numero', $dados['emenda_numero'], PDO::PARAM_INT);
        $stmt->bindParam(':emenda_valor', $dados['emenda_valor'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_descricao', $dados['emenda_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_status', $dados['emenda_status'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_orgao', $dados['emenda_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_estado', $dados['emenda_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_municipio', $dados['emenda_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_objetivo', $dados['emenda_objetivo'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_informacoes', $dados['emenda_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_tipo', $dados['emenda_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_id', $emenda_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para excluir uma emenda pelo seu ID.
     * 
     * @param string $emenda_id ID da emenda a ser excluída.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($emenda_id) {
        $query = "DELETE FROM emendas WHERE emenda_id = :emenda_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':emenda_id', $emenda_id, PDO::PARAM_STR);
        return $stmt->execute();
    }
}

<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe DocumentoTipoModel
 * 
 * Representa a model para operações na tabela `documentos_tipos`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class DocumentoTipoModel {

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe DocumentoTipoModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo tipo de documento.
     * 
     * @param array $dados Dados do tipo de documento (documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente)
                  VALUES (UUID(), :documento_tipo_nome, :documento_tipo_descricao, :documento_tipo_criado_por, :documento_tipo_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':documento_tipo_nome', $dados['documento_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_tipo_descricao', $dados['documento_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_tipo_criado_por', $dados['documento_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_tipo_cliente', $dados['documento_tipo_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um tipo de documento.
     * 
     * @param string $documento_tipo_id ID do tipo de documento a ser atualizado.
     * @param array $dados Novos dados do tipo de documento.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($documento_tipo_id, $dados) {
        $query = "UPDATE documentos_tipos SET documento_tipo_nome = :documento_tipo_nome, documento_tipo_descricao = :documento_tipo_descricao
                  WHERE documento_tipo_id = :documento_tipo_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':documento_tipo_nome', $dados['documento_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_tipo_descricao', $dados['documento_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_tipo_id', $documento_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os tipos de documentos.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os tipos de documentos.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM view_documentos_tipos WHERE documento_tipo_cliente = :cliente OR documento_tipo_cliente = 1 ORDER BY documento_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar tipos de documentos por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM documentos_tipos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um tipo de documento pelo seu ID.
     * 
     * @param string $documento_tipo_id ID do tipo de documento a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($documento_tipo_id) {
        $query = "DELETE FROM documentos_tipos WHERE documento_tipo_id = :documento_tipo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':documento_tipo_id', $documento_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

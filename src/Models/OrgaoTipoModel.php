<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe OrgaoTipoModel
 * 
 * Representa a model para operações na tabela `orgaos_tipos`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class OrgaoTipoModel {

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe OrgaoTipoModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo tipo de órgão.
     * 
     * @param array $dados Dados do tipo de órgão (orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO orgaos_tipos (orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente)
                  VALUES (:orgao_tipo_nome, :orgao_tipo_descricao, :orgao_tipo_criado_por, :orgao_tipo_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':orgao_tipo_nome', $dados['orgao_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_tipo_descricao', $dados['orgao_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_tipo_criado_por', $dados['orgao_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_tipo_cliente', $dados['orgao_tipo_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um tipo de órgão.
     * 
     * @param string $orgao_tipo_id ID do tipo de órgão a ser atualizado.
     * @param array $dados Novos dados do tipo de órgão.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($orgao_tipo_id, $dados) {
        $query = "UPDATE orgaos_tipos SET orgao_tipo_nome = :orgao_tipo_nome, orgao_tipo_descricao = :orgao_tipo_descricao
                  WHERE orgao_tipo_id = :orgao_tipo_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':orgao_tipo_nome', $dados['orgao_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_tipo_descricao', $dados['orgao_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_tipo_id', $orgao_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os tipos de órgãos.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os tipos de órgãos.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM view_orgaos_tipos WHERE orgao_tipo_cliente = :cliente OR orgao_tipo_cliente = 1 ORDER BY orgao_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar tipos de órgãos por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_orgaos_tipos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um tipo de órgão pelo seu ID.
     * 
     * @param string $orgao_tipo_id ID do tipo de órgão a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($orgao_tipo_id) {
        $query = "DELETE FROM orgaos_tipos WHERE orgao_tipo_id = :orgao_tipo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orgao_tipo_id', $orgao_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

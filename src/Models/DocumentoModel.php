<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe OficioModel
 *
 * Representa a model para operações na tabela `oficios`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 *
 * @package GabineteDigital\Models
 */
class DocumentoModel {
    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe OficioModel.
     *
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo ofício.
     *
     * @param array $dados Dados do ofício.
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO documentos (documento_titulo, documento_resumo, documento_arquivo, documento_ano, documento_tipo, documento_orgao, documento_criado_por, documento_cliente)
                  VALUES (:documento_titulo, :documento_resumo, :documento_arquivo, :documento_ano, :documento_tipo, :documento_orgao, :documento_criado_por, :documento_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':documento_titulo', $dados['documento_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_resumo', $dados['documento_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_arquivo', $dados['documento_arquivo'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_ano', $dados['documento_ano'], PDO::PARAM_INT);
        $stmt->bindParam(':documento_tipo', $dados['documento_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_orgao', $dados['documento_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_criado_por', $dados['documento_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_cliente', $dados['documento_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um ofício.
     *
     * @param string $documento_id ID do ofício a ser atualizado.
     * @param array $dados Novos dados do ofício.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($documento_id, $dados) {
        $query = "UPDATE documentos 
                  SET documento_titulo = :documento_titulo, 
                      documento_resumo = :documento_resumo, 
                      documento_arquivo = :documento_arquivo, 
                      documento_ano = :documento_ano, 
                      documento_tipo = :documento_tipo, 
                      documento_orgao = :documento_orgao 
                  WHERE documento_id = :documento_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':documento_titulo', $dados['documento_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_resumo', $dados['documento_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_arquivo', $dados['documento_arquivo'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_ano', $dados['documento_ano'], PDO::PARAM_INT);
        $stmt->bindParam(':documento_tipo', $dados['documento_tipo'], PDO::PARAM_STR);

        $stmt->bindParam(':documento_orgao', $dados['documento_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':documento_id', $documento_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar ofícios com base no ano e um termo de busca.
     *
     * @param int $ano Ano dos ofícios a serem filtrados.
     * @param string|null $busca Termo de busca para filtrar resultados pelo título ou resumo do ofício (opcional).
     * @return array Retorna um array contendo os resultados da consulta.
     */

    public function listar($ano, $tipo, $busca, $cliente) {


        if (empty($busca) && empty($tipo)) {
            $query = 'SELECT * FROM view_documentos WHERE documento_ano = :ano AND documento_cliente = :cliente ORDER BY documento_titulo DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ano', $ano, PDO::PARAM_STR);

            $stmt->bindValue(':cliente', $cliente, PDO::PARAM_STR);
        } else if (empty($busca) && !empty($tipo)) {
            $query = 'SELECT * FROM view_documentos WHERE documento_ano = :ano AND documento_tipo = :tipo AND documento_cliente = :cliente ORDER BY documento_titulo DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ano', $ano, PDO::PARAM_STR);
            $stmt->bindValue(':cliente', $cliente, PDO::PARAM_STR);
            $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        } else if (!empty($busca)) {
            $query = 'SELECT * FROM view_documentos WHERE documento_titulo LIKE :busca OR documento_resumo LIKE :busca AND documento_cliente = :cliente ORDER BY documento_titulo DESC';
            $stmt = $this->conn->prepare($query);
            $busca = '%' . $busca . '%';
            $stmt->bindValue(':busca', $busca, PDO::PARAM_STR);
            $stmt->bindValue(':cliente', $cliente, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Método para buscar ofícios por uma coluna específica e seu valor.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_documentos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um ofício pelo seu ID.
     *
     * @param string $documento_id ID do ofício a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($documento_id) {
        $query = "DELETE FROM documentos WHERE documento_id = :documento_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':documento_id', $documento_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

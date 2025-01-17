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
class OficioModel
{
    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe OficioModel.
     *
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo ofício.
     *
     * @param array $dados Dados do ofício.
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO oficios (oficio_titulo, oficio_resumo, oficio_arquivo, oficio_ano, oficio_orgao, oficio_criado_por, oficio_cliente)
                  VALUES (:oficio_titulo, :oficio_resumo, :oficio_arquivo, :oficio_ano, :oficio_orgao, :oficio_criado_por, :oficio_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':oficio_titulo', $dados['oficio_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_resumo', $dados['oficio_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_arquivo', $dados['oficio_arquivo'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_ano', $dados['oficio_ano'], PDO::PARAM_INT);
        $stmt->bindParam(':oficio_orgao', $dados['oficio_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_criado_por', $dados['oficio_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_cliente', $dados['oficio_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um ofício.
     *
     * @param string $oficio_id ID do ofício a ser atualizado.
     * @param array $dados Novos dados do ofício.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($oficio_id, $dados)
    {
        $query = "UPDATE oficios 
                  SET oficio_titulo = :oficio_titulo, 
                      oficio_resumo = :oficio_resumo, 
                      oficio_arquivo = :oficio_arquivo, 
                      oficio_ano = :oficio_ano, 
                      oficio_orgao = :oficio_orgao 
                  WHERE oficio_id = :oficio_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':oficio_titulo', $dados['oficio_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_resumo', $dados['oficio_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_arquivo', $dados['oficio_arquivo'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_ano', $dados['oficio_ano'], PDO::PARAM_INT);
        $stmt->bindParam(':oficio_orgao', $dados['oficio_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':oficio_id', $oficio_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar ofícios com base no ano e um termo de busca.
     *
     * @param int $ano Ano dos ofícios a serem filtrados.
     * @param string|null $busca Termo de busca para filtrar resultados pelo título ou resumo do ofício (opcional).
     * @return array Retorna um array contendo os resultados da consulta.
     */

    public function listar($ano, $busca, $cliente)
    {
        if ($busca === '') {
            // Busca apenas por ano
            $query = 'SELECT * FROM view_oficios WHERE oficio_ano = :ano AND oficio_cliente = :cliente ORDER BY oficio_titulo DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ano', $ano, PDO::PARAM_STR);
            $stmt->bindValue(':cliente', $cliente, PDO::PARAM_STR);
        } else {
            // Busca por título ou resumo, ignorando o ano
            $query = 'SELECT * FROM view_oficios WHERE oficio_titulo LIKE :busca OR oficio_resumo LIKE :busca AND oficio_cliente = :cliente ORDER BY oficio_titulo DESC';
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
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM oficios WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um ofício pelo seu ID.
     *
     * @param string $oficio_id ID do ofício a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($oficio_id)
    {
        $query = "DELETE FROM oficios WHERE oficio_id = :oficio_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':oficio_id', $oficio_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe ClippingModel
 *
 * Representa a model para operações na tabela `clipping`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 *
 * @package GabineteDigital\Models
 */
class ClippingModel
{
    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe ClippingModel.
     *
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo clipping.
     *
     * @param array $dados Dados do clipping.
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO clipping (clipping_resumo, clipping_titulo, clipping_data, clipping_link, clipping_orgao, clipping_arquivo, clipping_tipo, clipping_criado_por, clipping_cliente)
                  VALUES (:clipping_resumo, :clipping_titulo, :clipping_data, :clipping_link, :clipping_orgao, :clipping_arquivo, :clipping_tipo, :clipping_criado_por, :clipping_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':clipping_resumo', $dados['clipping_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_titulo', $dados['clipping_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_data', $dados['clipping_data'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_link', $dados['clipping_link'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_orgao', $dados['clipping_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_arquivo', $dados['clipping_arquivo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo', $dados['clipping_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_criado_por', $dados['clipping_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_cliente', $dados['clipping_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um clipping.
     *
     * @param string $clipping_id ID do clipping a ser atualizado.
     * @param array $dados Novos dados do clipping.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($clipping_id, $dados)
    {
        $query = "UPDATE clipping 
                  SET clipping_resumo = :clipping_resumo, 
                      clipping_titulo = :clipping_titulo, 
                      clipping_data = :clipping_data, 
                      clipping_link = :clipping_link, 
                      clipping_orgao = :clipping_orgao, 
                      clipping_arquivo = :clipping_arquivo, 
                      clipping_tipo = :clipping_tipo
                  WHERE clipping_id = :clipping_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':clipping_resumo', $dados['clipping_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_titulo', $dados['clipping_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_data', $dados['clipping_data'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_link', $dados['clipping_link'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_orgao', $dados['clipping_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_arquivo', $dados['clipping_arquivo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo', $dados['clipping_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_id', $clipping_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar clippings com base em um termo de busca.
     *
     * @param string|null $busca Termo de busca para filtrar resultados pelo título ou resumo do clipping (opcional).
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array contendo os resultados da consulta.
     */
    public function listar($busca, $ano, $cliente)
    {
        if ($busca === '') {
            $query = 'SELECT * FROM view_clipping WHERE YEAR(clipping_data) = :ano AND clipping_cliente = :cliente ORDER BY clipping_criado_em DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':cliente', $cliente, PDO::PARAM_STR);
            $stmt->bindValue(':ano', $ano, PDO::PARAM_STR);
        } else {
            $query = 'SELECT * FROM view_clipping WHERE (clipping_titulo LIKE :busca OR clipping_resumo LIKE :busca) AND clipping_cliente = :cliente ORDER BY clipping_criado_em DESC';
            $stmt = $this->conn->prepare($query);
            $busca = '%' . $busca . '%';
            $stmt->bindValue(':busca', $busca, PDO::PARAM_STR);
            $stmt->bindValue(':cliente', $cliente, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar clippings por uma coluna específica e seu valor.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_clipping  WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um clipping pelo seu ID.
     *
     * @param string $clipping_id ID do clipping a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($clipping_id)
    {
        $query = "DELETE FROM clipping WHERE clipping_id = :clipping_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clipping_id', $clipping_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para buscar clippings agrupados por ano.
     *
     * Retorna a contagem de clippings por ano, agrupados por ano e cliente.
     *
     * @param string $cliente ID do cliente associado aos clippings.
     *
     * @return array Retorna um array contendo o ano, a contagem de clippings e o total de clippings para o cliente.
     */
    public function buscarAno($cliente)
    {
        $query = "SELECT clipping_data, COUNT(*) as contagem, (SELECT COUNT(*) FROM view_clipping WHERE clipping_cliente = :cliente) AS total
        FROM view_clipping
        WHERE clipping_cliente = :cliente
        GROUP BY clipping_data 
        ORDER BY contagem DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar clippings agrupados por tipo e ano.
     *
     * Retorna a contagem de clippings por tipo, agrupados por ano e cliente.
     *
     * @param string $ano Ano do clipping a ser agrupado.
     * @param string $cliente ID do cliente associado aos clippings.
     *
     * @return array Retorna um array com o tipo do clipping, o nome do tipo e a contagem de clippings.
     */
    public function buscarTipo($ano, $cliente)
    {
        $query = "SELECT clipping_tipo, clipping_tipo_nome,  COUNT(*) as contagem, (SELECT COUNT(*) FROM view_clipping WHERE clipping_cliente = :cliente AND YEAR(clipping_data) = :ano) AS total
        FROM view_clipping
        WHERE clipping_cliente = :cliente
        AND YEAR(clipping_data) = :ano
        GROUP BY clipping_tipo  
        ORDER BY contagem DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':ano', $ano, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar clippings agrupados por órgão e ano.
     *
     * Retorna a contagem de clippings por órgão, agrupados por ano e cliente.
     *
     * @param string $ano Ano do clipping a ser agrupado.
     * @param string $cliente ID do cliente associado aos clippings.
     *
     * @return array Retorna um array com o órgão, nome do órgão e a contagem de clippings.
     */
    public function buscarOrgao($ano, $cliente)
    {
        $query = "SELECT clipping_orgao, orgao_nome,  COUNT(*) as contagem, (SELECT COUNT(*) FROM view_clipping WHERE clipping_cliente = :cliente AND YEAR(clipping_data) = :ano) AS total
        FROM view_clipping
        WHERE clipping_cliente = :cliente
        AND YEAR(clipping_data) = :ano
        GROUP BY orgao_nome, clipping_orgao
        ORDER BY contagem DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':ano', $ano, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

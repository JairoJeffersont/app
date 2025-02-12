<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe OrgaoModel
 *
 * Representa a model para operações na tabela `orgaos`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 *
 * @package GabineteDigital\Models
 */
class OrgaoModel
{

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe OrgaoModel.
     *
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo órgão.
     *
     * @param array $dados Dados do órgão.
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO orgaos (orgao_id, orgao_nome, orgao_email, orgao_telefone, orgao_endereco, orgao_bairro, orgao_municipio, orgao_estado, orgao_cep, orgao_tipo, orgao_informacoes, orgao_site, orgao_criado_por, orgao_cliente)
                  VALUES (UUID(), :orgao_nome, :orgao_email, :orgao_telefone, :orgao_endereco, :orgao_bairro, :orgao_municipio, :orgao_estado, :orgao_cep, :orgao_tipo, :orgao_informacoes, :orgao_site, :orgao_criado_por, :orgao_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':orgao_nome', $dados['orgao_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_email', $dados['orgao_email'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_telefone', $dados['orgao_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_endereco', $dados['orgao_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_bairro', $dados['orgao_bairro'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_municipio', $dados['orgao_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_estado', $dados['orgao_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_cep', $dados['orgao_cep'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_tipo', $dados['orgao_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_informacoes', $dados['orgao_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_site', $dados['orgao_site'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_criado_por', $dados['orgao_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_cliente', $dados['orgao_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um órgão.
     *
     * @param string $orgao_id ID do órgão a ser atualizado.
     * @param array $dados Novos dados do órgão.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($orgao_id, $dados)
    {
        $query = "UPDATE orgaos SET orgao_nome = :orgao_nome, orgao_email = :orgao_email, orgao_telefone = :orgao_telefone, orgao_endereco = :orgao_endereco, orgao_bairro = :orgao_bairro, orgao_municipio = :orgao_municipio, orgao_estado = :orgao_estado, orgao_cep = :orgao_cep, orgao_tipo = :orgao_tipo, orgao_informacoes = :orgao_informacoes, orgao_site = :orgao_site
                  WHERE orgao_id = :orgao_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':orgao_nome', $dados['orgao_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_email', $dados['orgao_email'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_telefone', $dados['orgao_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_endereco', $dados['orgao_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_bairro', $dados['orgao_bairro'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_municipio', $dados['orgao_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_estado', $dados['orgao_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_cep', $dados['orgao_cep'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_tipo', $dados['orgao_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_informacoes', $dados['orgao_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_site', $dados['orgao_site'], PDO::PARAM_STR);
        $stmt->bindParam(':orgao_id', $orgao_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar órgãos com paginação e filtros.
     *
     * Este método permite listar os órgãos com base em critérios específicos, 
     * como número de itens por página, ordem de classificação, termo de busca, estado e cliente associado.
     *
     * @param int $itens Número de itens a serem retornados por página.
     * @param int $pagina Número da página atual.
     * @param string $ordem Ordem de classificação (ASC ou DESC).
     * @param string $ordenarPor Nome da coluna pela qual os resultados serão ordenados.
     * @param string|null $termo Termo de busca para filtrar resultados pelo nome do órgão (opcional).
     * @param string|null $estado Filtro de estado para limitar os resultados (opcional).
     * @param int $cliente ID do cliente associado ao órgão.
     * @return array Retorna um array contendo os resultados da consulta e o total de itens encontrados.
     *      
     * O campo `total` indica o número total de registros encontrados com base nos filtros aplicados.
     */

    public function listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente)
    {
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        if ($termo === null) {
            if ($estado != null) {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_estado = '" . $estado . "' AND orgao_cliente = :cliente) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_estado = '" . $estado . "'  AND orgao_cliente = :cliente ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            } else {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1) AS total FROM view_orgaos WHERE orgao_id <> 1  AND orgao_cliente = :cliente ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            }
        } else {
            if ($estado != null) {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_estado = '" . $estado . "'  AND orgao_cliente = :cliente) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_estado = '" . $estado . "'  AND orgao_cliente = :cliente ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
                $termo = '%' . $termo . '%';
            } else {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo  AND orgao_cliente = :cliente) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_cliente = :cliente ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
                $termo = '%' . $termo . '%';
            }
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':cliente', $cliente, PDO::PARAM_INT);

        if ($termo !== null) {
            $stmt->bindValue(':termo', $termo, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar órgãos por uma coluna específica e seu valor.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM orgaos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um órgão pelo seu ID.
     *
     * @param string $orgao_id ID do órgão a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($orgao_id)
    {
        $query = "DELETE FROM orgaos WHERE orgao_id = :orgao_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orgao_id', $orgao_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

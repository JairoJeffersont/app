<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe PessoaModel
 *
 * Representa a model para operações na tabela `pessoas`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 *
 * @package GabineteDigital\Models
 */
class PessoaModel
{

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe PessoaModel.
     *
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar uma nova pessoa.
     *
     * @param array $dados Dados da pessoa.
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO pessoas (pessoa_nome, pessoa_aniversario, pessoa_email, pessoa_telefone, pessoa_endereco, pessoa_bairro, pessoa_municipio, pessoa_estado, pessoa_cep, pessoa_sexo, pessoa_facebook, pessoa_instagram, pessoa_x, pessoa_informacoes, pessoa_profissao, pessoa_cargo, pessoa_tipo, pessoa_orgao, pessoa_foto, pessoa_criada_por, pessoa_cliente)
                  VALUES (:pessoa_nome, :pessoa_aniversario, :pessoa_email, :pessoa_telefone, :pessoa_endereco, :pessoa_bairro, :pessoa_municipio, :pessoa_estado, :pessoa_cep, :pessoa_sexo, :pessoa_facebook, :pessoa_instagram, :pessoa_x, :pessoa_informacoes, :pessoa_profissao, :pessoa_cargo, :pessoa_tipo, :pessoa_orgao, :pessoa_foto, :pessoa_criada_por, :pessoa_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pessoa_nome', $dados['pessoa_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_aniversario', $dados['pessoa_aniversario'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_email', $dados['pessoa_email'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_telefone', $dados['pessoa_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_endereco', $dados['pessoa_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_bairro', $dados['pessoa_bairro'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_municipio', $dados['pessoa_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_estado', $dados['pessoa_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_cep', $dados['pessoa_cep'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_sexo', $dados['pessoa_sexo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_facebook', $dados['pessoa_facebook'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_instagram', $dados['pessoa_instagram'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_x', $dados['pessoa_x'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_informacoes', $dados['pessoa_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_profissao', $dados['pessoa_profissao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_cargo', $dados['pessoa_cargo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_tipo', $dados['pessoa_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_orgao', $dados['pessoa_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_foto', $dados['pessoa_foto'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_criada_por', $dados['pessoa_criada_por'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_cliente', $dados['pessoa_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de uma pessoa.
     *
     * @param string $pessoa_id ID da pessoa a ser atualizada.
     * @param array $dados Novos dados da pessoa.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($pessoa_id, $dados)
    {
        $query = "UPDATE pessoas SET pessoa_nome = :pessoa_nome, pessoa_aniversario = :pessoa_aniversario, pessoa_email = :pessoa_email, pessoa_telefone = :pessoa_telefone, pessoa_endereco = :pessoa_endereco, pessoa_bairro = :pessoa_bairro, pessoa_municipio = :pessoa_municipio, pessoa_estado = :pessoa_estado, pessoa_cep = :pessoa_cep, pessoa_sexo = :pessoa_sexo, pessoa_facebook = :pessoa_facebook, pessoa_instagram = :pessoa_instagram, pessoa_x = :pessoa_x, pessoa_informacoes = :pessoa_informacoes, pessoa_profissao = :pessoa_profissao, pessoa_cargo = :pessoa_cargo, pessoa_tipo = :pessoa_tipo, pessoa_orgao = :pessoa_orgao, pessoa_foto = :pessoa_foto
                  WHERE pessoa_id = :pessoa_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pessoa_nome', $dados['pessoa_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_aniversario', $dados['pessoa_aniversario'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_email', $dados['pessoa_email'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_telefone', $dados['pessoa_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_endereco', $dados['pessoa_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_bairro', $dados['pessoa_bairro'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_municipio', $dados['pessoa_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_estado', $dados['pessoa_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_cep', $dados['pessoa_cep'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_sexo', $dados['pessoa_sexo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_facebook', $dados['pessoa_facebook'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_instagram', $dados['pessoa_instagram'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_x', $dados['pessoa_x'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_informacoes', $dados['pessoa_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_profissao', $dados['pessoa_profissao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_cargo', $dados['pessoa_cargo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_tipo', $dados['pessoa_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_orgao', $dados['pessoa_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_foto', $dados['pessoa_foto'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_id', $pessoa_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar pessoas com paginação e filtros.
     *
     * @param int $itens Número de itens a serem retornados por página.
     * @param int $pagina Número da página atual.
     * @param string $ordem Ordem de classificação (ASC ou DESC).
     * @param string $ordenarPor Nome da coluna pela qual os resultados serão ordenados.
     * @param string|null $termo Termo de busca para filtrar resultados pelo nome da pessoa (opcional).
     * @param string|null $estado Filtro de estado para limitar os resultados (opcional).
     * @param int $cliente ID do cliente associado à pessoa.
     * @return array Retorna um array contendo os resultados da consulta e o total de itens encontrados.
     */
    public function listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente)
    {
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        if ($termo === null) {
            if ($estado != null) {
                $query = "SELECT view_pessoas.*, 
                                 (SELECT COUNT(*) FROM pessoas WHERE pessoa_estado = '" . $estado . "' AND pessoa_cliente = :cliente) AS total
                          FROM view_pessoas
                          WHERE pessoa_estado = '" . $estado . "' AND pessoa_cliente = :cliente
                          ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            } else {
                $query = "SELECT view_pessoas.*, 
                                 (SELECT COUNT(*) FROM pessoas WHERE pessoa_cliente = :cliente) AS total
                          FROM view_pessoas
                          WHERE pessoa_cliente = :cliente
                          ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            }
        } else {
            if ($estado != null) {
                $query = "SELECT view_pessoas.*, 
                                 (SELECT COUNT(*) FROM pessoas WHERE pessoa_nome LIKE :termo AND pessoa_estado = '" . $estado . "' AND pessoa_cliente = :cliente) AS total
                          FROM view_pessoas
                          WHERE pessoa_nome LIKE :termo AND pessoa_estado = '" . $estado . "' AND pessoa_cliente = :cliente
                          ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
                $termo = '%' . $termo . '%';
            } else {
                $query = "SELECT view_pessoas.*, 
                                 (SELECT COUNT(*) FROM pessoas WHERE pessoa_nome LIKE :termo AND pessoa_cliente = :cliente) AS total
                          FROM view_pessoas
                          WHERE pessoa_nome LIKE :termo AND pessoa_cliente = :cliente
                          ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
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
     * Método para buscar pessoas por uma coluna específica e seu valor.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_pessoas WHERE $coluna = :valor ORDER BY pessoa_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar uma pessoa pelo seu ID.
     *
     * @param string $pessoa_id ID da pessoa a ser deletada.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($pessoa_id)
    {
        $query = "DELETE FROM pessoas WHERE pessoa_id = :pessoa_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pessoa_id', $pessoa_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function buscarSexo($estado, $cliente)
    {
        if ($estado != null) {
            $query = "SELECT pessoa_sexo, COUNT(*) as contagem, (SELECT COUNT(*) FROM pessoas WHERE pessoa_cliente = :cliente AND pessoa_estado = :estado) AS total
            FROM view_pessoas
            WHERE pessoa_cliente = :cliente 
            AND pessoa_estado = :estado
            GROUP BY pessoa_sexo
            ORDER BY contagem DESC";
        } else {
            $query = "SELECT pessoa_sexo, COUNT(*) as contagem, (SELECT COUNT(*) FROM pessoas WHERE pessoa_cliente = :cliente ) AS total
              FROM view_pessoas
              WHERE pessoa_cliente = :cliente 
              GROUP BY pessoa_sexo
              ORDER BY contagem DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProfissao($estado, $cliente)
    {
        if ($estado != null) {
            $query = "SELECT pessoa_profissao, pessoas_profissoes_nome, COUNT(*) as contagem, (SELECT COUNT(*) FROM view_pessoas WHERE pessoa_cliente = :cliente AND pessoa_estado = :estado) AS total
            FROM view_pessoas
            WHERE pessoa_cliente = :cliente 
            AND pessoa_estado = :estado
            GROUP BY pessoa_profissao
            ORDER BY contagem DESC";
        } else {
            $query = "SELECT pessoa_profissao, pessoas_profissoes_nome, COUNT(*) as contagem, (SELECT COUNT(*) FROM view_pessoas WHERE pessoa_cliente = :cliente ) AS total
              FROM view_pessoas
              WHERE pessoa_cliente = :cliente 
              GROUP BY pessoa_profissao
              ORDER BY contagem DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarMunicipio($estado, $cliente)
    {
        if ($estado != null) {
            $query = "SELECT pessoa_municipio, pessoa_estado, COUNT(*) as contagem, (SELECT COUNT(*) FROM view_pessoas WHERE pessoa_cliente = :cliente AND pessoa_estado = :estado) AS total
            FROM view_pessoas
            WHERE pessoa_cliente = :cliente 
            AND pessoa_estado = :estado
            GROUP BY pessoa_municipio
            ORDER BY contagem DESC";
        } else {
            $query = "SELECT pessoa_municipio, pessoa_estado, COUNT(*) as contagem, (SELECT COUNT(*) FROM view_pessoas WHERE pessoa_cliente = :cliente ) AS total
              FROM view_pessoas
              WHERE pessoa_cliente = :cliente 
              GROUP BY pessoa_municipio, pessoa_estado
              ORDER BY contagem DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarBairro($municipio, $cliente)
    {
        $query = "SELECT pessoa_bairro, COUNT(*) as contagem, (SELECT COUNT(*) FROM view_pessoas WHERE pessoa_cliente = :cliente AND pessoa_municipio = :municipio) AS total
        FROM view_pessoas
        WHERE pessoa_cliente = :cliente 
        AND pessoa_municipio = :municipio
        GROUP BY pessoa_bairro 
        ORDER BY contagem DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':municipio', $municipio, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

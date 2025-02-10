<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe AgendaSituacaoModel
 * 
 * Representa a model para operações na tabela `agenda_situacao`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class AgendaSituacaoModel
{
    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe AgendaSituacaoModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar uma nova situação de agenda.
     * 
     * @param array $dados Dados da situação da agenda (agenda_situacao_nome, agenda_situacao_descricao, agenda_situacao_criado_por, agenda_situacao_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO agenda_situacao (agenda_situacao_nome, agenda_situacao_descricao, agenda_situacao_criado_por, agenda_situacao_cliente)
                  VALUES (:agenda_situacao_nome, :agenda_situacao_descricao, :agenda_situacao_criado_por, :agenda_situacao_cliente)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_situacao_nome', $dados['agenda_situacao_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_situacao_descricao', $dados['agenda_situacao_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_situacao_criado_por', $dados['agenda_situacao_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_situacao_cliente', $dados['agenda_situacao_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de uma situação de agenda.
     * 
     * @param string $agenda_situacao_id ID da situação de agenda a ser atualizada.
     * @param array $dados Novos dados da situação de agenda.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($agenda_situacao_id, $dados)
    {
        $query = "UPDATE agenda_situacao SET agenda_situacao_nome = :agenda_situacao_nome, agenda_situacao_descricao = :agenda_situacao_descricao
                  WHERE agenda_situacao_id = :agenda_situacao_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_situacao_nome', $dados['agenda_situacao_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_situacao_descricao', $dados['agenda_situacao_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_situacao_id', $agenda_situacao_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todas as situações de agenda.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todas as situações de agenda.
     */
    public function listar($cliente)
    {
        $query = "SELECT * FROM view_agenda_situacao WHERE agenda_situacao_cliente = :cliente OR agenda_situacao_cliente = '1' ORDER BY agenda_situacao_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar situações de agenda por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_agenda_situacao WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar uma situação de agenda pelo seu ID.
     * 
     * @param string $agenda_situacao_id ID da situação de agenda a ser deletada.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($agenda_situacao_id)
    {
        $query = "DELETE FROM agenda_situacao WHERE agenda_situacao_id = :agenda_situacao_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_situacao_id', $agenda_situacao_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

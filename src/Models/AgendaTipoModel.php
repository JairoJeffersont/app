<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe AgendaTipoModel
 * 
 * Representa a model para operações na tabela `agenda_tipo`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class AgendaTipoModel
{
    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe AgendaTipoModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo tipo de agenda.
     * 
     * @param array $dados Dados do tipo de agenda (agenda_tipo_nome, agenda_tipo_descricao, agenda_tipo_criado_por, agenda_tipo_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO agenda_tipo (agenda_tipo_nome, agenda_tipo_descricao, agenda_tipo_criado_por, agenda_tipo_cliente)
                  VALUES (:agenda_tipo_nome, :agenda_tipo_descricao, :agenda_tipo_criado_por, :agenda_tipo_cliente)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_tipo_nome', $dados['agenda_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_tipo_descricao', $dados['agenda_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_tipo_criado_por', $dados['agenda_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_tipo_cliente', $dados['agenda_tipo_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um tipo de agenda.
     * 
     * @param string $agenda_tipo_id ID do tipo de agenda a ser atualizado.
     * @param array $dados Novos dados do tipo de agenda.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($agenda_tipo_id, $dados)
    {
        $query = "UPDATE agenda_tipo SET agenda_tipo_nome = :agenda_tipo_nome, agenda_tipo_descricao = :agenda_tipo_descricao
                  WHERE agenda_tipo_id = :agenda_tipo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_tipo_nome', $dados['agenda_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_tipo_descricao', $dados['agenda_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_tipo_id', $agenda_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os tipos de agendas.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todos os tipos de agendas.
     */
    public function listar($cliente)
    {
        $query = "SELECT * FROM view_agenda_tipo WHERE agenda_tipo_cliente = :cliente OR agenda_tipo_cliente = '1' ORDER BY agenda_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar tipos de agendas por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_agenda_tipo WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um tipo de agenda pelo seu ID.
     * 
     * @param string $agenda_tipo_id ID do tipo de agenda a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($agenda_tipo_id)
    {
        $query = "DELETE FROM agenda_tipo WHERE agenda_tipo_id = :agenda_tipo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_tipo_id', $agenda_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

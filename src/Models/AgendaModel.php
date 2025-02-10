<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe AgendaModel
 * 
 * Representa a model para operações na tabela `agenda`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class AgendaModel
{
    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe AgendaModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar uma nova agenda.
     * 
     * @param array $dados Dados da agenda (agenda_titulo, agenda_situacao, agenda_tipo, agenda_data, agenda_local, agenda_estado, agenda_informacoes, agenda_criada_por, agenda_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO agenda (agenda_titulo, agenda_situacao, agenda_tipo, agenda_data, agenda_local, agenda_estado, agenda_informacoes, agenda_criada_por, agenda_cliente)
                  VALUES (:agenda_titulo, :agenda_situacao, :agenda_tipo, :agenda_data, :agenda_local, :agenda_estado, :agenda_informacoes, :agenda_criada_por, :agenda_cliente)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_titulo', $dados['agenda_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_situacao', $dados['agenda_situacao'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_tipo', $dados['agenda_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_data', $dados['agenda_data'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_local', $dados['agenda_local'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_estado', $dados['agenda_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_informacoes', $dados['agenda_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_criada_por', $dados['agenda_criada_por'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_cliente', $dados['agenda_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de uma agenda.
     * 
     * @param string $agenda_id ID da agenda a ser atualizada.
     * @param array $dados Novos dados da agenda.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($agenda_id, $dados)
    {
        $query = "UPDATE agenda SET agenda_titulo = :agenda_titulo, agenda_situacao = :agenda_situacao, agenda_tipo = :agenda_tipo,
                  agenda_data = :agenda_data, agenda_local = :agenda_local, agenda_estado = :agenda_estado, 
                  agenda_informacoes = :agenda_informacoes, agenda_atualizada_em = CURRENT_TIMESTAMP 
                  WHERE agenda_id = :agenda_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_titulo', $dados['agenda_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_situacao', $dados['agenda_situacao'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_tipo', $dados['agenda_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_data', $dados['agenda_data'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_local', $dados['agenda_local'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_estado', $dados['agenda_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_informacoes', $dados['agenda_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':agenda_id', $agenda_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todas as agendas de um cliente.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todas as agendas.
     */
    public function listar($cliente, $data)
    {
        // Ajusta a consulta para buscar pela data específica
        $query = "SELECT * FROM view_agenda WHERE agenda_cliente = :cliente AND DATE(agenda_data) = :data ORDER BY agenda_data ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':data', $data, PDO::PARAM_STR);  // Aqui bind a data que você vai passar

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Método para buscar uma agenda por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_agenda WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar uma agenda pelo seu ID.
     * 
     * @param string $agenda_id ID da agenda a ser deletada.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($agenda_id)
    {
        $query = "DELETE FROM agenda WHERE agenda_id = :agenda_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agenda_id', $agenda_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

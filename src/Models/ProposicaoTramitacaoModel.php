<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe ProposicaoTramitacaoModel
 * 
 * Representa a model para operações na tabela `proposicoes_tramitacoes`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class ProposicaoTramitacaoModel
{

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe ProposicaoTramitacaoModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar uma nova proposição de trâmitação.
     * 
     * @param array $dados Dados da proposição de trâmitação (proposicao_tramitacao_nome, proposicao_tramitacao_descricao, proposicao_tramitacao_criada_por, proposicao_tramitacao_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO proposicoes_tramitacoes (proposicao_tramitacao_id, proposicao_tramitacao_nome, proposicao_tramitacao_descricao, proposicao_tramitacao_criada_por, proposicao_tramitacao_cliente)
                  VALUES (UUID(), :proposicao_tramitacao_nome, :proposicao_tramitacao_descricao, :proposicao_tramitacao_criada_por, :proposicao_tramitacao_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':proposicao_tramitacao_nome', $dados['proposicao_tramitacao_nome'], PDO::PARAM_INT);
        $stmt->bindParam(':proposicao_tramitacao_descricao', $dados['proposicao_tramitacao_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tramitacao_criada_por', $dados['proposicao_tramitacao_criada_por'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tramitacao_cliente', $dados['proposicao_tramitacao_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de uma proposição de trâmitação.
     * 
     * @param string $proposicao_tramitacao_id ID da proposição de trâmitação a ser atualizada.
     * @param array $dados Novos dados da proposição de trâmitação.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($proposicao_tramitacao_id, $dados)
    {
        $query = "UPDATE proposicoes_tramitacoes SET proposicao_tramitacao_nome = :proposicao_tramitacao_nome, proposicao_tramitacao_descricao = :proposicao_tramitacao_descricao
                  WHERE proposicao_tramitacao_id = :proposicao_tramitacao_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':proposicao_tramitacao_nome', $dados['proposicao_tramitacao_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tramitacao_descricao', $dados['proposicao_tramitacao_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tramitacao_id', $proposicao_tramitacao_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todas as proposições de trâmitação.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todas as proposições de trâmitação.
     */
    public function listar($cliente)
    {
        $query = "SELECT * FROM view_proposicoes_tramitacoes WHERE proposicao_tramitacao_cliente = :cliente or proposicao_tramitacao_cliente = 1 ORDER BY proposicao_tramitacao_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar proposições de trâmitação por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_proposicoes_tramitacoes WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar uma proposição de trâmitação pelo seu ID.
     * 
     * @param string $proposicao_tramitacao_id ID da proposição de trâmitação a ser deletada.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($proposicao_tramitacao_id)
    {
        $query = "DELETE FROM proposicoes_tramitacoes WHERE proposicao_tramitacao_id = :proposicao_tramitacao_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':proposicao_tramitacao_id', $proposicao_tramitacao_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe NotaTecnicaModel
 * 
 * Representa a model para operações na tabela `nota_tecnica`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class NotaTecnicaModel
{

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe NotaTecnicaModel.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar uma nova nota técnica.
     * 
     * @param array $dados Dados da nota técnica (nota_proposicao, nota_proposicao_apelido, nota_proposicao_resumo, nota_texto, nota_criada_por, nota_cliente).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados)
    {
        $query = "INSERT INTO nota_tecnica (nota_id, nota_proposicao, nota_proposicao_apelido, nota_proposicao_resumo, nota_proposicao_tema, nota_texto, nota_criada_por, nota_cliente)
                  VALUES (UUID(), :nota_proposicao, :nota_proposicao_apelido, :nota_proposicao_resumo, :nota_proposicao_tema, :nota_texto, :nota_criada_por, :nota_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nota_proposicao', $dados['nota_proposicao'], PDO::PARAM_INT);
        $stmt->bindParam(':nota_proposicao_apelido', $dados['nota_proposicao_apelido'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_proposicao_resumo', $dados['nota_proposicao_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_proposicao_tema', $dados['nota_proposicao_tema'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_texto', $dados['nota_texto'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_criada_por', $dados['nota_criada_por'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_cliente', $dados['nota_cliente'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de uma nota técnica.
     * 
     * @param string $nota_id ID da nota técnica a ser atualizada.
     * @param array $dados Novos dados da nota técnica.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($nota_id, $dados)
    {
        $query = "UPDATE nota_tecnica SET 
                    nota_proposicao_apelido = :nota_proposicao_apelido, 
                    nota_proposicao_resumo = :nota_proposicao_resumo, 
                    nota_texto = :nota_texto, 
                    nota_proposicao_tema = :nota_proposicao_tema
                  WHERE nota_id = :nota_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nota_proposicao_apelido', $dados['nota_proposicao_apelido'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_proposicao_resumo', $dados['nota_proposicao_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_proposicao_tema', $dados['nota_proposicao_tema'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_texto', $dados['nota_texto'], PDO::PARAM_STR);
        $stmt->bindParam(':nota_id', $nota_id, PDO::PARAM_STR);

        return $stmt->execute();
    }


    /**
     * Método para listar todas as notas técnicas de um cliente.
     * 
     * @param string $cliente ID do cliente associado.
     * @return array Retorna um array associativo com os dados de todas as notas técnicas.
     */
    public function listar($cliente)
    {
        $query = "SELECT * FROM view_notas WHERE nota_cliente = :cliente ORDER BY nota_criada_em DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar notas técnicas por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados encontrados.
     */
    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_notas WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar uma nota técnica pelo seu ID.
     * 
     * @param string $nota_id ID da nota técnica a ser deletada.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($nota_id)
    {
        $query = "DELETE FROM nota_tecnica WHERE nota_id = :nota_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nota_id', $nota_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

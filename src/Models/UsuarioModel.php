<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe Usuario
 * 
 * Representa a model para operações na tabela `usuario`.
 * Utiliza a classe `Database` para conexão com o banco de dados.
 * 
 * @package GabineteDigital\Models
 */
class UsuarioModel {

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe Usuario.
     * 
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo usuário.
     * 
     * @param array $dados Dados do usuário (usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_nivel, usuario_ativo, usuario_aniversario, usuario_cliente, usuario_foto).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO usuario (usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_nivel, usuario_ativo, usuario_aniversario, usuario_cliente, usuario_foto)
                  VALUES (:usuario_nome, :usuario_email, :usuario_telefone, :usuario_senha, :usuario_nivel, :usuario_ativo, :usuario_aniversario, :usuario_cliente, :usuario_foto)";

        $stmt = $this->conn->prepare($query);

        $senha = password_hash($dados['usuario_senha'], PASSWORD_DEFAULT);

        $stmt->bindParam(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_senha', $senha, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_nivel', $dados['usuario_nivel'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_cliente', $dados['usuario_cliente'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_foto', $dados['usuario_foto'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um usuário.
     * 
     * @param string $usuario_id ID do usuário a ser atualizado.
     * @param array $dados Novos dados do usuário.
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($usuario_id, $dados) {
        $query = "UPDATE usuario SET usuario_nome = :usuario_nome, usuario_email = :usuario_email, usuario_telefone = :usuario_telefone, 
                  usuario_senha = :usuario_senha, usuario_nivel = :usuario_nivel, usuario_ativo = :usuario_ativo, usuario_aniversario = :usuario_aniversario, 
                  usuario_cliente = :usuario_cliente, usuario_foto = :usuario_foto, usuario_token = :usuario_token
                  WHERE usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($query);

        $token = isset($dados['usuario_token']) ? $dados['usuario_token'] : null;

        // Bind dos parâmetros.
        $stmt->bindParam(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_senha', $dados['usuario_senha'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_nivel', $dados['usuario_nivel'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_cliente', $dados['usuario_cliente'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_foto', $dados['usuario_foto'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Método para listar todos os usuários.
     * 
     * @return array Retorna um array associativo com os dados de todos os usuários.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM view_usuarios WHERE usuario_cliente = :cliente AND usuario_id <> '1' ORDER BY usuario_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar usuários por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados dos usuários encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_usuarios WHERE $coluna = :valor AND usuario_id <> '1'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para alterar o status de um usuário (ativo/inativo).
     * 
     * @param string $usuario_id ID do usuário cujo status será alterado.
     * @param int $status Novo status do usuário (0 para inativo, 1 para ativo).
     * @return bool Retorna `true` se a alteração foi bem-sucedida, `false` caso contrário.
     */
    public function mudarStatus($usuario_id, $status) {
        $query = "UPDATE usuario SET usuario_ativo = :status WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Método para apagar um usuário pelo seu ID.
     * 
     * @param string $usuario_id ID do usuário a ser deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($usuario_id) {
        $query = "DELETE FROM usuario WHERE usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

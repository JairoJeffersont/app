<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe Cliente
 * 
 * A classe Cliente representa a model de clientes na aplicação. 
 * Esta classe contém métodos para criação, atualização, exclusão, 
 * listagem e busca de clientes no banco de dados.
 * 
 * Utiliza a classe `Database` para estabelecer conexão com o banco de dados 
 * e a extensão `PDO` para realizar as operações SQL.
 * 
 * @package GabineteDigital\Models
 */
class ClienteModel {

    /** @var PDO Conexão com o banco de dados */
    private $conn;

    /**
     * Construtor da classe Cliente.
     * 
     * Inicializa a conexão com o banco de dados usando a classe `Database`.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Método para criar um novo cliente.
     * 
     * @param array $dados Associativo com os dados do cliente (cliente_nome, cliente_email, cliente_telefone, cliente_endereco, cliente_cep, cliente_cpf_cnpj, cliente_ativo, cliente_assinaturas, cliente_deputado_id, cliente_deputado_nome, cliente_deputado_estado).
     * @return bool Retorna `true` se a inserção foi bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO cliente (cliente_nome, cliente_email, cliente_telefone, cliente_endereco, cliente_cep, cliente_cpf, cliente_ativo, cliente_assinaturas, cliente_deputado_nome, cliente_deputado_estado, cliente_deputado_tipo)
                  VALUES (:cliente_nome, :cliente_email, :cliente_telefone, :cliente_endereco, :cliente_cep, :cliente_cpf, :cliente_ativo, :cliente_assinaturas, :cliente_deputado_nome, :cliente_deputado_estado, :cliente_deputado_tipo)";

        $stmt = $this->conn->prepare($query);

        // Ligação dos parâmetros da query aos valores fornecidos.
        $stmt->bindParam(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_assinaturas', $dados['cliente_assinaturas'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_nome', $dados['cliente_deputado_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_cpf', $dados['cliente_cpf'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_cep', $dados['cliente_cep'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_endereco', $dados['cliente_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_deputado_estado', $dados['cliente_deputado_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_deputado_tipo', $dados['cliente_deputado_tipo'], PDO::PARAM_STR);


        return $stmt->execute();
    }

    /**
     * Método para atualizar as informações de um cliente existente.
     * 
     * @param int $cliente_id ID do cliente que será atualizado.
     * @param array $dados Associativo com os novos dados do cliente (cliente_nome, cliente_email, cliente_telefone, cliente_endereco, cliente_cep, cliente_cpf, cliente_ativo, cliente_assinaturas, cliente_deputado_id, cliente_deputado_nome, cliente_deputado_estado).
     * @return bool Retorna `true` se a atualização foi bem-sucedida, `false` caso contrário.
     */
    public function atualizar($cliente_id, $dados) {
        $query = "UPDATE cliente SET cliente_nome = :cliente_nome, cliente_email = :cliente_email, 
                  cliente_telefone = :cliente_telefone, cliente_ativo = :cliente_ativo, cliente_assinaturas = :cliente_assinaturas, cliente_endereco = :cliente_endereco, cliente_cep = :cliente_cep, cliente_cpf = :cliente_cpf, cliente_deputado_nome = :cliente_deputado_nome, cliente_deputado_estado = :cliente_deputado_estado, 
                  cliente_deputado_tipo = :cliente_deputado_tipo WHERE cliente_id = :cliente_id";

        $stmt = $this->conn->prepare($query);

        // Ligação dos parâmetros da query aos valores fornecidos.
        $stmt->bindParam(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_assinaturas', $dados['cliente_assinaturas'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_nome', $dados['cliente_deputado_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_cpf', $dados['cliente_cpf'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_cep', $dados['cliente_cep'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_endereco', $dados['cliente_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_deputado_estado', $dados['cliente_deputado_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_tipo', $dados['cliente_deputado_tipo'], PDO::PARAM_STR);


        return $stmt->execute();
    }

    /**
     * Método para mudar o status de um cliente (ativo/inativo).
     * 
     * @param string $cliente_id ID do cliente cujo status será alterado.
     * @param int $status Novo status do cliente (0 para inativo, 1 para ativo).
     * @return bool Retorna `true` se a alteração foi bem-sucedida, `false` caso contrário.
     */
    public function mudarStatusCliente($cliente_id, $status) {
        $query = "UPDATE cliente JOIN usuario ON cliente.cliente_id = usuario.usuario_cliente SET cliente_ativo = :status, usuario_ativo = :status WHERE cliente.cliente_id = :cliente_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Método para listar todos os clientes.
     * 
     * @return array Retorna um array associativo com os dados de todos os clientes, exceto o cliente com ID 1.
     */
    public function listar() {
        $query = "SELECT * FROM cliente WHERE cliente_id <> '1' ORDER BY cliente_criado_em DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para buscar clientes por uma coluna específica e seu valor.
     * 
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna.
     * @return array Retorna um array associativo com os dados dos clientes encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM cliente WHERE $coluna = :valor AND cliente_id <> '1'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para apagar um cliente pelo seu ID.
     * 
     * @param int $cliente_id ID do cliente que será deletado.
     * @return bool Retorna `true` se a exclusão foi bem-sucedida, `false` caso contrário.
     */
    public function apagar($cliente_id) {
        $query = "DELETE FROM cliente WHERE cliente_id = :cliente_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

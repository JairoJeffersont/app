<?php

namespace GabineteDigital\Middleware;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use PDO;
use PDOException;

/**
 * Classe Database
 *
 * Responsável por gerenciar a conexão com o banco de dados.
 */
class Database {

    /** @var PDO $connection Instância do PDO para conexão com o banco de dados */
    private $connection;

    /**
     * Construtor da classe Database.
     *
     * Inicializa a conexão com o banco de dados utilizando os parâmetros configurados.
     * Em caso de erro na conexão, registra o erro no log e redireciona para a página de erro fatal.
     */
    public function __construct() {
        // Instancia do logger para erros relacionados à conexão com o banco de dados
        $log = new Logger('error_db');

        // Caminho para o diretório de logs
        $log->pushHandler(new StreamHandler(dirname(__DIR__, 2) . '/logs/error_db.log', Level::Error));

        // Obtém a configuração do banco de dados
        $config = require dirname(__DIR__, 2) . '/src/Configs/config.php';

        // Obtém os dados de configuração
        $host = $config['database']['host'];
        $dbname = $config['database']['name'];
        $username = $config['database']['user'];
        $password = $config['database']['password'];

        try {
            // Criação da conexão PDO
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

            // Configuração de atributos da conexão PDO
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Define o modo de erro como exceção
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Define o modo de busca padrão como ASSOCIATIVO
            $this->connection->exec("SET NAMES 'utf8mb4'"); // Configura o charset como UTF-8MB4

        } catch (PDOException $e) {
            // Registra o erro no log
            $log->log(Level::Error, $e->getMessage());

            // Redireciona para página de erro em caso de falha na conexão
            header('Location: ?secao=fatal_error');
            exit;
        }
    }

    /**
     * Obtém a instância da conexão PDO.
     *
     * @return PDO A instância do PDO para conexão com o banco de dados.
     */
    public function getConnection() {
        return $this->connection;
    }
}

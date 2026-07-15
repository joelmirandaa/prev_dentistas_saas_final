<?php

namespace App\Database;

use PDO;
use Exception;

/**
 * Class Connection
 * Gerencia a instância única de conexão PDO com o banco de dados.
 */
class Connection
{
    private static ?PDO $instance = null;

    /**
     * Retorna a conexão PDO (padrão Singleton)
     * 
     * @return PDO
     * @throws Exception
     */
    public static function getInstance(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Carrega configurações
        $configPath = realpath(__DIR__ . '/../../config/database.php');
        if (!$configPath || !file_exists($configPath)) {
            throw new Exception("Arquivo de configuração do banco de dados não encontrado.");
        }

        $config = require $configPath;

        $host     = $config['host'] ?? 'localhost';
        $port     = $config['port'] ?? '3306';
        $db_name  = $config['db_name'] ?? '';
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';

        try {
            self::$instance = new PDO(
                "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8",
                $username,
                $password
            );
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            // Mensagem amigável para o usuário
            throw new Exception("Erro ao conectar ao banco de dados. Verifique as configurações.");
        }

        return self::$instance;
    }
}

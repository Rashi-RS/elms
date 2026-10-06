<?php
declare(strict_types=1);

namespace App\models;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?self $instance = null;
    private PDO $connection;
    private string $driver = 'mysql';

    private function __construct()
    {
        $config = require APP_ROOT . '/config/database.php';
        $this->driver = $config['driver'] ?? 'mysql';

        try {
            if ($config['driver'] === 'sqlite') {
                $dbFile = str_ends_with((string)$config['database'], '.sqlite') ? $config['database'] : 'elms.sqlite';
                $dbPath = APP_ROOT . '/database/' . $dbFile;
                $this->connection = new PDO('sqlite:' . $dbPath, null, null, $config['options']);
                $this->connection->exec('PRAGMA foreign_keys = ON;');
                // SQLite compatibility functions matching MySQL dialect
                $this->connection->sqliteCreateFunction('NOW', fn() => date('Y-m-d H:i:s'));
                $this->connection->sqliteCreateFunction('CURDATE', fn() => date('Y-m-d'));
                $this->connection->sqliteCreateFunction('MONTH', fn($date) => $date ? (int)date('m', strtotime((string)$date)) : null);
                $this->connection->sqliteCreateFunction('YEAR', fn($date) => $date ? (int)date('Y', strtotime((string)$date)) : null);
                $this->connection->sqliteCreateFunction('CONCAT', fn(...$args) => implode('', $args));
                $this->connection->sqliteCreateFunction('IF', fn($cond, $trueVal, $falseVal) => $cond ? $trueVal : $falseVal);
            } else {
                $dsn = sprintf(
                    '%s:host=%s;port=%d;dbname=%s;charset=%s',
                    $config['driver'],
                    $config['host'],
                    $config['port'],
                    $config['database'],
                    $config['charset']
                );
                $this->connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            }
        } catch (PDOException $e) {
            // Write to system log and throw clean runtime exception
            error_log("Database connection error: " . $e->getMessage());
            throw new RuntimeException("Database connection failure. Please verify database credentials and ensure MySQL is running.");
        }
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserializing
    public function __wakeup()
    {
        throw new RuntimeException("Cannot unserialize singleton");
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function isSqlite(): bool
    {
        return $this->driver === 'sqlite';
    }

    public static function pdo(): PDO
    {
        return self::getInstance()->getConnection();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }
}

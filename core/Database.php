<?php

declare(strict_types=1);

namespace core;

use PDO;
use PDOStatement;
use Throwable;

class Database
{
    public readonly PDO $pdo;
    private string $table = '';
    private array $wheres = [];
    private array $params = [];
    private string $orderBy = '';
    private int $limit = 0;

    public function __construct(array $config)
    {
        $driver = $config['driver'] ?? 'sqlite';
        
        try {
            switch ($driver) {
                case 'mysql':
                    $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", 
                        $config['host'] ?? 'localhost', 
                        $config['port'] ?? '3306', 
                        $config['dbname'] ?? ''
                    );
                    $this->pdo = new PDO($dsn, $config['user'] ?? 'root', $config['password'] ?? '', [
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]);
                    break;
                
                case 'pgsql':
                    $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s", 
                        $config['host'] ?? 'localhost', 
                        $config['port'] ?? '5432', 
                        $config['dbname'] ?? ''
                    );
                    $this->pdo = new PDO($dsn, $config['user'] ?? 'postgres', $config['password'] ?? '');
                    break;

                case 'sqlite':
                default:
                    $dbPath = $config['db_path'] ?? Application::$ROOT_DIR . '/storage/db/database.sqlite';
                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0775, true);
                    }
                    $this->pdo = new PDO("sqlite:$dbPath");
                    break;
            }
            
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (Throwable $e) {
            Logger::error("Database Connection Failed ($driver): " . $e->getMessage());
            die("Database Error ($driver). Check logs for details.");
        }
    }

    /**
     * Query Builder: Start a query on a table
     */
    public function table(string $name): self
    {
        $this->table = $name;
        $this->wheres = [];
        $this->params = [];
        $this->orderBy = '';
        $this->limit = 0;
        return $this;
    }

    public function where(string $column, $value, string $operator = '='): self
    {
        $this->wheres[] = "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = " ORDER BY $column $direction";
        return $this;
    }

    public function limit(int $count): self
    {
        $this->limit = $count;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $sql .= $this->orderBy;
        if ($this->limit > 0) {
            $sql .= " LIMIT {$this->limit}";
        }

        return $this->fetchAll($sql, $this->params);
    }

    public function first(): array|bool
    {
        $this->limit(1);
        $res = $this->get();
        return $res[0] ?? false;
    }

    /**
     * Raw Query execution
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (Throwable $e) {
            Logger::error("SQL Query Failed: " . $e->getMessage() . " | SQL: $sql");
            throw $e;
        }
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): array|bool
    {
        return $this->query($sql, $params)->fetch();
    }

    public function applyMigrations(): void
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $files = scandir(Application::$ROOT_DIR . '/migrations');
        $toApplyMigrations = array_diff($files ?? [], $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            
            Logger::info("Applying migration: $migration");
            $instance->up();
            $this->saveMigrations([$migration]);
            Logger::info("Applied migration: $migration");
        }
    }

    private function createMigrationsTable(): void
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $pk = match ($driver) {
            'mysql' => "INT AUTO_INCREMENT PRIMARY KEY",
            'pgsql' => "SERIAL PRIMARY KEY",
            default => "INTEGER PRIMARY KEY AUTOINCREMENT"
        };

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id $pk,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    private function getAppliedMigrations(): array
    {
        return $this->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
    }

    private function saveMigrations(array $migrations): void
    {
        foreach ($migrations as $migration) {
            $this->query("INSERT INTO migrations (migration) VALUES (?)", [$migration]);
        }
    }

    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
}

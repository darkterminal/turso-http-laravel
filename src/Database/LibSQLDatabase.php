<?php

namespace Turso\Http\Laravel\Database;

use Darkterminal\TursoHttp\LibSQL;
use Turso\Http\Laravel\Exceptions\ConfigurationIsNotFound;

class LibSQLDatabase
{
    protected LibSQL $db;

    protected array $config;

    protected string $connection_mode;

    protected array $lastInsertIds = [];

    protected bool $inTransaction = false;

    public function __construct(array $config = [])
    {
        if (empty($config['authToken']) && empty($config['url'])) {
            throw new ConfigurationIsNotFound("DB_DATABASE and DB_AUTH_TOKEN cannot be empty. Use only DB_DATABASE when use 'turso dev' command");
        }

        $this->db = empty($config['authToken']) ? $this->createLibSQL($config['url']) : $this->createLibSQL("dbname={$config['url']}&authToken={$config['authToken']}");
    }

    protected function createLibSQL(string $config): LibSQL
    {
        return new LibSQL($config);
    }

    public function version(): string
    {
        return $this->getDb()->version();
    }

    public function beginTransaction(): bool
    {
        $this->inTransaction = $this->prepare('BEGIN')->execute();

        return $this->inTransaction;
    }

    public function commit(): bool
    {
        $result = $this->prepare('COMMIT')->execute();

        $this->inTransaction = false;

        return $result;
    }

    public function exec(string $queryStatement): int
    {
        $statement = $this->prepare($queryStatement);
        $statement->execute();

        return $statement->rowCount();
    }

    public function prepare(string $sql): LibSQLPDOStatement
    {
        return new LibSQLPDOStatement($this, $sql);
    }

    public function query(string $sql, array $params = [])
    {
        return $this->db->query($sql, $params)->fetchArray(LibSQL::LIBSQL_ASSOC);
    }

    public function setLastInsertId(?string $name = null, ?int $value = null): void
    {
        if ($name === null) {
            $name = 'id';
        }

        $this->lastInsertIds[$name] = $value;
    }

    public function lastInsertId(?string $name = null): string|false
    {
        if ($name === null) {
            $name = 'id';
        }

        return isset($this->lastInsertIds[$name])
            ? (string) $this->lastInsertIds[$name]
            : false;
    }

    public function rollBack(): bool
    {
        $result = $this->prepare('ROLLBACK')->execute();

        $this->inTransaction = false;

        return $result;
    }

    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    public function sync(): void
    {
        throw new \Exception('[LibSQL: remote] Sync is only available for Remote Replica Connection in Native libSQL Extension.', 1);
    }

    public function getDb(): LibSQL
    {
        return $this->db;
    }

    public function getConnectionMode(): string
    {
        return $this->connection_mode;
    }

    public function escapeString($input)
    {
        if ($input === null) {
            return 'NULL';
        }

        return \SQLite3::escapeString($input);
    }

    public function quote($input)
    {
        if ($input === null) {
            return 'NULL';
        }

        return "'".$this->escapeString($input)."'";
    }

    public function __destruct()
    {
        if (isset($this->db)) {
            $this->db->close();
        }
    }
}

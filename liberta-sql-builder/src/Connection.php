<?php

namespace Liberta\Sql;

use PDO;
use PDOException;

final class Connection
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $driver  = $config['driver'] ?? 'mysql';
        $host    = $config['host'] ?? 'localhost';
        $port    = $config['port'] ?? null;
        $charset = $config['charset'] ?? 'utf8';
        $dbname  = $config['database'];

        $dsn = "{$driver}:host={$host}"
             . ($port ? ";port={$port}" : '')
             . ";dbname={$dbname};charset={$charset}";

        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}

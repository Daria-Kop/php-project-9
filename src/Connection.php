<?php

namespace App;

final class Connection
{
    private static ?Connection $conn = null;

    public function connect()
    {
        if (!isset($_ENV['DATABASE_URL'])) {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
        }

        $url = $_ENV['DATABASE_URL'];
        $params = parse_url($url);

        if ($params === false) {
            throw new \Exception("Error reading database configuration file");
        }

        $conStr = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $params['host'] ?? '',
            $params['port'] ?? '',
            ltrim($params['path'] ?? '', '/'),
            $params['user'] ?? '',
            $params['pass'] ?? ''
        );

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        return $pdo;
    }

    public static function get()
    {
        if (static::$conn === null) {
            static::$conn = new self();
        }

        return static::$conn;
    }

    protected function __construct()
    {
    }
}

<?php

namespace App\Db;

use PDO;

class DB
{

    private static $instance;

    private PDO $connection;

    public function __construct()
    {
        $this->connection = new PDO('sqlite::memory:');
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // FAKE DATABASE MIGRATION

        $this->connection->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                password TEXT NOT NULL
            )
        ");

        $this->connection->exec("
            INSERT INTO users (name, email, password) VALUES
            ('Walid', 'walid@test.com', 'test1'),
            ('Mohamed', 'mohamed@test.com', 'test2'),
            ('Wafaa', 'wafaa@test.com', 'test3')
        ");
    }

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}

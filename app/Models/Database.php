<?php

class Database
{
    private PDO $connection;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'] ?? "";
        $data_source_name = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try {
            $this->connection = new PDO($data_source_name, $user, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //force pdo à écrire l'erreur si il y en a une
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //associative array mode
                PDO::ATTR_EMULATE_PREPARES   => false, //force des requêtes sql préparer
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}

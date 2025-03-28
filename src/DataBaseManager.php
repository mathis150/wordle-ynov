<?php
declare(strict_types=1);

class DataBaseManager {
    private PDO $db;

    public function __construct(PDO $pdo = null) {
        if ($pdo === null) {
            $host = $_ENV['DB_HOST'];
            $db_name = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];

            try {
                $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $db_name, $username, $password);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                echo 'Connection error: ' . $e->getMessage();
            }
        } else {
            $this->db = $pdo;
        }
    }

    public function executeDataBase(string $query, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
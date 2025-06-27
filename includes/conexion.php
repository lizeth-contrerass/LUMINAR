<?php
class Database {
    private $host = "localhost";
    private $db_name = "LUMINAR";
    private $username = "root";
    private $password = "n0m3l0";
    private $charset = "utf8mb4";
    private $conn;

    public function connect() {
        if ($this->conn) {
            return $this->conn;
        }

        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => true,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Puedes personalizar este error
            die("Conexión fallida: " . $e->getMessage());
        }
        return $this->conn;
    }
}
<?php


class Database {
    private static $instance = null;
    private $conn;
    
    private $host = 'localhost';
    private $db_name = 'bookshop';
    private $username = 'isslem';
    private $password = '123123456isslem';

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // Journalisation de l'erreur
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            throw new Exception("Échec de la connexion à la base de données");
        }
    }

    // Méthode pour obtenir la connexion (version corrigée)
    public static function getConnection() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }

    // Empêcher le clonage
    private function __clone() {}
    
    // Empêcher la désérialisation
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
<?php
namespace Config;

use PDO;
use PDOException;
use App\Exceptions\DatabaseException;

class Database {
    private static ?PDO $connection = null;
    
    private function __construct() {}
    
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=localhost;dbname=inventory_system;charset=utf8mb4";
                $username = "root";  // Default Laragon
                $password = "";      // Default Laragon (kosong)
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                self::$connection = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                throw new DatabaseException("Connection failed: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}

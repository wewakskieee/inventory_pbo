<?php
namespace App\Controllers;

use PDO;
use App\Exceptions\DatabaseException;

class CategoryController extends BaseController {
    private PDO $connection;
    
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }
    
    public function index(): void {
        try {
            $stmt = $this->connection->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll();
            
            $this->jsonResponse([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\PDOException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

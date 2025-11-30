<?php
namespace App\Repositories;

use PDO;
use App\Exceptions\DatabaseException;

class ProductRepository implements RepositoryInterface {
    private PDO $connection;
    
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }
    
    public function findAll(): array {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        
        try {
            $stmt = $this->connection->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    
    public function findById(int $id): ?array {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO products (sku, name, description, category_id, quantity, price, minimum_stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                $data['sku'],
                $data['name'],
                $data['description'] ?? '',
                $data['category_id'],
                $data['quantity'],
                $data['price'],
                $data['minimum_stock']
            ]);
            return (int) $this->connection->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    
    public function update(int $id, array $data): bool {
        $sql = "UPDATE products 
                SET sku = ?, name = ?, description = ?, category_id = ?, 
                    quantity = ?, price = ?, minimum_stock = ? 
                WHERE id = ?";
        
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([
                $data['sku'],
                $data['name'],
                $data['description'] ?? '',
                $data['category_id'],
                $data['quantity'],
                $data['price'],
                $data['minimum_stock'],
                $id
            ]);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM products WHERE id = ?";
        
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    
    public function search(string $keyword): array {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?
                ORDER BY p.created_at DESC";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $searchTerm = "%$keyword%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    
    public function filterByCategory(int $categoryId): array {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ?
                ORDER BY p.created_at DESC";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$categoryId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
}

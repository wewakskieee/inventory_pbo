<?php
namespace App\Database;

use PDO;
use App\Exceptions\DatabaseException;

class QueryBuilder {
    private PDO $connection;
    private string $table = '';
    private array $wheres = [];
    private array $bindings = [];
    private string $orderBy = '';
    private int $limit = 0;
    private int $offset = 0;
    
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }
    
    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }
    
    public function where(string $column, string $operator, $value): self {
        $this->wheres[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }
    
    public function orWhere(string $column, string $operator, $value): self {
        $connector = empty($this->wheres) ? '' : 'OR ';
        $this->wheres[] = $connector . "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }
    
    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }
    
    public function offset(int $offset): self {
        $this->offset = $offset;
        return $this;
    }
    
    public function get(): array {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        if ($this->orderBy) {
            $sql .= " " . $this->orderBy;
        }
        
        if ($this->limit > 0) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset > 0) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($this->bindings);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new DatabaseException("Query failed: " . $e->getMessage());
        }
    }
    
    public function first(): ?array {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    public function count(): int {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($this->bindings);
            return (int) $stmt->fetch()['total'];
        } catch (\PDOException $e) {
            throw new DatabaseException("Count query failed: " . $e->getMessage());
        }
    }
}

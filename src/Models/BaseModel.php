<?php
namespace App\Models;

use PDO;

abstract class BaseModel {
    protected PDO $connection;
    protected string $table;
    
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }
    
    abstract protected function getTableName(): string;
}

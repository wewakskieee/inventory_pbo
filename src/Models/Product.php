<?php
namespace App\Models;

class Product extends BaseModel {
    private int $id;
    private string $sku;
    private string $name;
    private string $description;
    private int $categoryId;
    private int $quantity;
    private float $price;
    private int $minimumStock;
    
    protected function getTableName(): string {
        return 'products';
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function getSku(): string {
        return $this->sku;
    }
    
    public function setSku(string $sku): void {
        $this->sku = $sku;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function setName(string $name): void {
        $this->name = $name;
    }
    
    public function getDescription(): string {
        return $this->description;
    }
    
    public function setDescription(string $description): void {
        $this->description = $description;
    }
    
    public function getCategoryId(): int {
        return $this->categoryId;
    }
    
    public function setCategoryId(int $categoryId): void {
        $this->categoryId = $categoryId;
    }
    
    public function getQuantity(): int {
        return $this->quantity;
    }
    
    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }
    
    public function getPrice(): float {
        return $this->price;
    }
    
    public function setPrice(float $price): void {
        $this->price = $price;
    }
    
    public function getMinimumStock(): int {
        return $this->minimumStock;
    }
    
    public function setMinimumStock(int $minimumStock): void {
        $this->minimumStock = $minimumStock;
    }
    
    public function isLowStock(): bool {
        return $this->quantity <= $this->minimumStock;
    }
}

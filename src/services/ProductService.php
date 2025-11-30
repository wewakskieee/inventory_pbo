<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use App\Services\ProductValidator;

class ProductService {
    private ProductRepository $repository;
    private ProductValidator $validator;
    
    public function __construct(ProductRepository $repository, ProductValidator $validator) {
        $this->repository = $repository;
        $this->validator = $validator;
    }
    
    public function getAllProducts(): array {
        return $this->repository->findAll();
    }
    
    public function getProductById(int $id): ?array {
        return $this->repository->findById($id);
    }
    
    public function createProduct(array $data): int {
        $validatedData = $this->validator->validate($data);
        return $this->repository->create($validatedData);
    }
    
    public function updateProduct(int $id, array $data): bool {
        $validatedData = $this->validator->validate($data);
        return $this->repository->update($id, $validatedData);
    }
    
    public function deleteProduct(int $id): bool {
        return $this->repository->delete($id);
    }
    
    public function searchProducts(string $keyword): array {
        return $this->repository->search($keyword);
    }
    
    public function filterProductsByCategory(int $categoryId): array {
        return $this->repository->filterByCategory($categoryId);
    }
    
    public function getLowStockProducts(): array {
        $allProducts = $this->repository->findAll();
        return array_filter($allProducts, function($product) {
            return $product['quantity'] <= $product['minimum_stock'];
        });
    }
}

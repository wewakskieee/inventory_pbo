<?php
namespace App\Controllers;

use App\Services\ProductService;
use App\Exceptions\ValidationException;
use App\Exceptions\DatabaseException;

class ProductController extends BaseController {
    private ProductService $productService;
    
    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }
    
    public function index(): void {
        try {
            $products = $this->productService->getAllProducts();
            $this->jsonResponse([
                'success' => true,
                'data' => $products
            ]);
        } catch (DatabaseException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show(int $id): void {
        try {
            $product = $this->productService->getProductById($id);
            
            if (!$product) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }
            
            $this->jsonResponse([
                'success' => true,
                'data' => $product
            ]);
        } catch (DatabaseException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function store(array $data): void {
        try {
            $productId = $this->productService->createProduct($data);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => ['id' => $productId]
            ], 201);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->getErrors()
            ], 422);
        } catch (DatabaseException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function update(int $id, array $data): void {
        try {
            $success = $this->productService->updateProduct($id, $data);
            
            if ($success) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Produk berhasil diupdate'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Gagal mengupdate produk'
                ], 500);
            }
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->getErrors()
            ], 422);
        } catch (DatabaseException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(int $id): void {
        try {
            $success = $this->productService->deleteProduct($id);
            
            if ($success) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Gagal menghapus produk'
                ], 500);
            }
        } catch (DatabaseException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function search(string $keyword): void {
        try {
            $products = $this->productService->searchProducts($keyword);
            $this->jsonResponse([
                'success' => true,
                'data' => $products
            ]);
        } catch (DatabaseException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function filterByCategory(int $categoryId): void {
        try {
            $products = $this->productService->filterProductsByCategory($categoryId);
            $this->jsonResponse([
                'success' => true,
                'data' => $products
            ]);
        } catch (DatabaseException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

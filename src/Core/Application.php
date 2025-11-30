<?php
namespace App\Core;

use Config\Database;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use App\Services\ProductValidator;

class Application {
    private Router $router;
    
    public function __construct() {
        $this->router = new Router();
        $this->registerRoutes();
    }
    
    private function registerRoutes(): void {
        $connection = Database::getConnection();
        
        $productRepository = new ProductRepository($connection);
        $productValidator = new ProductValidator();
        $productService = new ProductService($productRepository, $productValidator);
        $productController = new ProductController($productService);
        
        $categoryController = new CategoryController($connection);
        
        $this->router->get('/api/products', function($request) use ($productController) {
            if ($request->get('search')) {
                $productController->search($request->get('search'));
            } elseif ($request->get('category')) {
                $productController->filterByCategory((int) $request->get('category'));
            } else {
                $productController->index();
            }
        });
        
        $this->router->get('/api/products/{id}', function($request, $id) use ($productController) {
            $productController->show((int) $id);
        });
        
        $this->router->post('/api/products', function($request) use ($productController) {
            $productController->store($request->getData());
        });
        
        $this->router->put('/api/products/{id}', function($request, $id) use ($productController) {
            $productController->update((int) $id, $request->getData());
        });
        
        $this->router->delete('/api/products/{id}', function($request, $id) use ($productController) {
            $productController->destroy((int) $id);
        });
        
        $this->router->get('/api/categories', function($request) use ($categoryController) {
            $categoryController->index();
        });
    }
    
    public function run(): void {
        $request = new Request();
        $this->router->dispatch($request);
    }
}

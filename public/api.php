<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load autoload
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use App\Services\ProductValidator;

try {
    // Get request info
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_GET['path'] ?? '';
    
    // Parse request body untuk PUT/DELETE
    $requestBody = [];
    if (in_array($method, ['PUT', 'DELETE', 'POST'])) {
        $input = file_get_contents('php://input');
        $requestBody = json_decode($input, true) ?? $_POST;
    }
    
    // Setup dependencies
    $connection = Database::getConnection();
    $productRepository = new ProductRepository($connection);
    $productValidator = new ProductValidator();
    $productService = new ProductService($productRepository, $productValidator);
    $productController = new ProductController($productService);
    $categoryController = new CategoryController($connection);
    
    // Routing
    switch ($path) {
        case 'categories':
            if ($method === 'GET') {
                $categoryController->index();
            }
            break;
            
        case 'products':
            if ($method === 'GET') {
                if (isset($_GET['search'])) {
                    $productController->search($_GET['search']);
                } elseif (isset($_GET['category'])) {
                    $productController->filterByCategory((int) $_GET['category']);
                } else {
                    $productController->index();
                }
            } elseif ($method === 'POST') {
                $productController->store($requestBody);
            }
            break;
            
        case (preg_match('/^products\/(\d+)$/', $path, $matches) ? true : false):
            $id = (int) $matches[1];
            
            if ($method === 'GET') {
                $productController->show($id);
            } elseif ($method === 'PUT') {
                $productController->update($id, $requestBody);
            } elseif ($method === 'DELETE') {
                $productController->destroy($id);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Endpoint not found: ' . $path
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

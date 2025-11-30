<?php
namespace App\Controllers;

abstract class BaseController {
    protected function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
    
    protected function view(string $viewName, array $data = []): void {
        extract($data);
        require_once __DIR__ . "/../Views/$viewName.php";
    }
}

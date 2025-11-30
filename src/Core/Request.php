<?php
namespace App\Core;

class Request {
    private string $method;
    private string $uri;
    private array $data;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->data = $this->parseRequestData();
    }
    
    private function parseRequestData(): array {
        $data = [];
        
        if ($this->method === 'GET') {
            $data = $_GET;
        } elseif ($this->method === 'POST') {
            $data = $_POST;
        } elseif (in_array($this->method, ['PUT', 'DELETE', 'PATCH'])) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true) ?? [];
        }
        
        return $data;
    }
    
    public function getMethod(): string {
        return $this->method;
    }
    
    public function getUri(): string {
        return $this->uri;
    }
    
    public function getData(): array {
        return $this->data;
    }
    
    public function get(string $key, $default = null) {
        return $this->data[$key] ?? $default;
    }
}

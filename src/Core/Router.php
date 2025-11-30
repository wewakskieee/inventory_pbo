<?php
namespace App\Core;

class Router {
    private array $routes = [];
    
    public function get(string $path, callable $handler): void {
        $this->routes['GET'][$path] = $handler;
    }
    
    public function post(string $path, callable $handler): void {
        $this->routes['POST'][$path] = $handler;
    }
    
    public function put(string $path, callable $handler): void {
        $this->routes['PUT'][$path] = $handler;
    }
    
    public function delete(string $path, callable $handler): void {
        $this->routes['DELETE'][$path] = $handler;
    }
    
    public function dispatch(Request $request): void {
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = $this->convertRouteToRegex($route);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($handler, array_merge([$request], $matches));
                return;
            }
        }
        
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Route not found']);
    }
    
    private function convertRouteToRegex(string $route): string {
        $route = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([0-9]+)', $route);
        return '#^' . $route . '$#';
    }
}

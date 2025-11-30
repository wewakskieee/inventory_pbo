<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Autoload...</h2>";

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';
echo "✓ Composer autoload loaded<br><br>";

// Test classes
echo "<h3>Testing Classes:</h3>";
$tests = [
    'Config\\Database',
    'App\\Repositories\\ProductRepository',
    'App\\Services\\ProductService',
    'App\\Services\\ProductValidator',
    'App\\Controllers\\ProductController',
    'App\\Controllers\\CategoryController',
    'App\\Exceptions\\ValidationException',
    'App\\Exceptions\\DatabaseException',
];

foreach ($tests as $class) {
    if (class_exists($class)) {
        echo "✓ Class found: <strong>$class</strong><br>";
    } else {
        echo "✗ Class NOT found: <strong>$class</strong><br>";
    }
}

echo "<br><h3>Testing Database Connection:</h3>";
try {
    $conn = Config\Database::getConnection();
    echo "✓ Database connection successful!<br>";
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM products");
    $result = $stmt->fetch();
    echo "✓ Total products in database: " . $result['total'] . "<br>";
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM categories");
    $result = $stmt->fetch();
    echo "✓ Total categories in database: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "✗ Database Error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Testing API Endpoints:</h3>";
echo "Test these URLs:<br>";
echo "- <a href='api.php?path=categories' target='_blank'>api.php?path=categories</a><br>";
echo "- <a href='api.php?path=products' target='_blank'>api.php?path=products</a><br>";

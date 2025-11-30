-- database.sql
CREATE DATABASE IF NOT EXISTS inventory_system;
USE inventory_system;

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    minimum_stock INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_sku (sku),
    INDEX idx_name (name),
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Data
INSERT INTO categories (name, description) VALUES
('Elektronik', 'Perangkat elektronik dan gadget'),
('Furniture', 'Perabotan kantor dan rumah'),
('Alat Tulis', 'Perlengkapan tulis dan kantor'),
('Pakaian', 'Produk fashion dan garmen'),
('Makanan', 'Produk makanan dan minuman');

INSERT INTO products (sku, name, description, category_id, quantity, price, minimum_stock) VALUES
('ELK-001', 'Laptop Dell XPS 13', 'Laptop premium dengan Intel Core i7', 1, 15, 15000000.00, 5),
('ELK-002', 'Mouse Wireless Logitech', 'Mouse wireless ergonomis', 1, 50, 250000.00, 10),
('FUR-001', 'Meja Kantor Premium', 'Meja kantor kayu jati', 2, 8, 2500000.00, 3),
('FUR-002', 'Kursi Ergonomis', 'Kursi kantor dengan sandaran punggung', 2, 20, 1500000.00, 5),
('ATK-001', 'Pulpen Pilot', 'Pulpen tinta hitam', 3, 200, 5000.00, 50),
('ATK-002', 'Buku Tulis A4', 'Buku tulis 100 halaman', 3, 150, 15000.00, 30),
('PKN-001', 'Kemeja Formal Pria', 'Kemeja lengan panjang', 4, 30, 350000.00, 10),
('MKN-001', 'Kopi Arabica Premium', 'Kopi arabica 250g', 5, 100, 75000.00, 20);

INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@inventory.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('staff', 'staff@inventory.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff');

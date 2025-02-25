-- Kreiranje baze podataka ako ne postoji
CREATE DATABASE IF NOT EXISTS solarni_paneli;
USE solarni_paneli;

-- Table: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('klijent', 'kompanija') NOT NULL DEFAULT 'klijent',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: admins
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    company_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    FOREIGN KEY (company_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    status ENUM('pending', 'processed', 'canceled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table: order_logs
CREATE TABLE order_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    old_status ENUM('pending', 'processed', 'canceled') NOT NULL,
    new_status ENUM('pending', 'processed', 'canceled') NOT NULL,
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Insert Default Admin
INSERT INTO admins (username, password) VALUES ('admin', SHA2('admin123', 256));

-- Sample Users (clients and companies)
INSERT INTO users (username, email, password, role) VALUES
('client1', 'client1@example.com', SHA2('password1', 256), 'klijent'),
('client2', 'client2@example.com', SHA2('password2', 256), 'klijent'),
('company1', 'company1@example.com', SHA2('password3', 256), 'kompanija'),
('company2', 'company2@example.com', SHA2('password4', 256), 'kompanija');

-- Sample Products (linked to companies)
INSERT INTO products (name, description, price, company_id) VALUES
('Solarni Panel A', 'Opis panela A', 250.00, 3),
('Solarni Panel B', 'Opis panela B', 300.00, 3),
('Solarni Inverter', 'Opis invertora', 500.00, 4),
('Baterija X', 'Opis baterije X', 150.00, 4);

-- Sample Orders (linked to clients and products)
INSERT INTO orders (user_id, product_id, quantity, status) VALUES
(1, 1, 2, 'pending'),
(1, 2, 1, 'processed'),
(2, 3, 1, 'pending'),
(2, 4, 3, 'canceled');

-- Sample Order Logs
INSERT INTO order_logs (order_id, old_status, new_status) VALUES
(1, 'pending', 'processed'),
(2, 'processed', 'canceled');

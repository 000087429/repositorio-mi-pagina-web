CREATE DATABASE IF NOT EXISTS pizzeria_trejo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pizzeria_trejo;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(120) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  sale_date DATE NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (full_name, email, password_hash, role, status) VALUES
('Administrador', 'admin@pizzeria.com', 'pizzeria123', 'admin', 'active'),
('Cliente Demo', 'user@pizzeria.com', 'pizzeria123', 'user', 'active');

INSERT INTO sales (customer_name, total_amount, sale_date, description) VALUES
('Juan Pérez', 320.50, CURDATE(), 'Pedido de 2 pizzas grandes'),
('María López', 185.00, CURDATE(), 'Pedido de 1 pizza y bebida');

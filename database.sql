-- Grocerific Database Setup
-- Create database and table for grocery items

CREATE DATABASE IF NOT EXISTS grocerific;
USE grocerific;

-- Create table for grocery items
CREATE TABLE IF NOT EXISTS grocery_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert some sample data
INSERT INTO grocery_items (name, category, quantity, price, description) VALUES
('Apples', 'Fruits', 10, 2.99, 'Fresh red apples'),
('Milk', 'Dairy', 5, 3.49, 'Whole milk - 1 gallon'),
('Bread', 'Bakery', 8, 2.49, 'Whole wheat bread'),
('Bananas', 'Fruits', 15, 1.99, 'Yellow bananas'),
('Chicken Breast', 'Meat', 3, 8.99, 'Boneless chicken breast');
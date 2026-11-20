CREATE DATABASE IF NOT EXISTS zy_collection CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zy_collection;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL,
  category ENUM('Men','Women','Shoes','Accessories') NOT NULL DEFAULT 'Women',
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255) DEFAULT 'placeholder.svg',
  featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) DEFAULT NULL,
  address TEXT NOT NULL,
  payment_method ENUM('Zaad','eDahab') NOT NULL,
  payment_reference VARCHAR(120) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status ENUM('Pending','Confirmed','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  payment_status ENUM('Unverified','Verified','Rejected') NOT NULL DEFAULT 'Unverified',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(180) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO products (name, category, description, price, stock, image, featured) VALUES
('Classic Linen Shirt', 'Men', 'A clean, breathable everyday linen shirt.', 28.00, 15, 'placeholder.svg', 1),
('Essential Oversized Tee', 'Men', 'A relaxed fit essential for daily wear.', 18.00, 20, 'placeholder.svg', 1),
('Elegant Satin Dress', 'Women', 'An elegant dress for evenings and special occasions.', 45.00, 10, 'placeholder.svg', 1),
('Everyday Abaya', 'Women', 'Minimal, comfortable and timeless.', 38.00, 12, 'placeholder.svg', 1),
('Minimal Leather Sneakers', 'Shoes', 'Versatile sneakers with a clean silhouette.', 52.00, 8, 'placeholder.svg', 0),
('Classic Shoulder Bag', 'Accessories', 'A simple statement piece for everyday use.', 34.00, 9, 'placeholder.svg', 0);

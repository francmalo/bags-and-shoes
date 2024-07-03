-- Create database if not exists
CREATE DATABASE IF NOT EXISTS ecommerce_products;

-- Use the database
USE ecommerce_products;

-- Table: Categories
CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (name)
);

-- Table: Types
CREATE TABLE Types (
    type_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id),
    UNIQUE KEY (category_id, name)
);

-- Table: Brands
CREATE TABLE Brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (name)
);

-- New Table: Genders
CREATE TABLE Genders (
    gender_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    UNIQUE KEY (name)
);

-- Table: Products (modified to include gender_id)
CREATE TABLE Products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    type_id INT NOT NULL,
    brand_id INT NOT NULL,
    gender_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id),
    FOREIGN KEY (type_id) REFERENCES Types(type_id),
    FOREIGN KEY (brand_id) REFERENCES Brands(brand_id),
    FOREIGN KEY (gender_id) REFERENCES Genders(gender_id),
    UNIQUE KEY (name, brand_id, gender_id)
);

-- Table: Product_Photos
CREATE TABLE Product_Photos (
    photo_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

-- Table: Sizes
CREATE TABLE Sizes (
    size_id INT AUTO_INCREMENT PRIMARY KEY,
    size_value VARCHAR(10) NOT NULL,
    UNIQUE KEY (size_value)
);

-- Table: Colors
CREATE TABLE Colors (
    color_id INT AUTO_INCREMENT PRIMARY KEY,
    color_name VARCHAR(50) NOT NULL,
    UNIQUE KEY (color_name)
);

-- Table: Product_Attributes (to store sizes and colors per product)
CREATE TABLE Product_Attributes (
    product_id INT NOT NULL,
    size_id INT NOT NULL,
    color_id INT NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES Products(product_id),
    FOREIGN KEY (size_id) REFERENCES Sizes(size_id),
    FOREIGN KEY (color_id) REFERENCES Colors(color_id),
    PRIMARY KEY (product_id, size_id, color_id)
);

-- Table: Orders
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: Order_Items (to store products in each order)
CREATE TABLE Order_Items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    size_id INT NOT NULL,
    color_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id),
    FOREIGN KEY (product_id, size_id, color_id) REFERENCES Product_Attributes(product_id, size_id, color_id)
);

-- Insert default gender values
INSERT INTO Genders (name) VALUES ('Men'), ('Women'), ('Unisex');





-- Insert Categories
INSERT INTO Categories (name, description) VALUES
('Bags', 'Various types of bags for all occasions'),
('Shoes', 'Footwear for men and women');

-- Insert Types
INSERT INTO Types (category_id, name, description) VALUES
(1, 'Backpack', 'Comfortable bags for everyday use and travel'),
(1, 'Tote', 'Spacious and stylish bags for work or shopping'),
(1, 'Clutch', 'Small, elegant bags for evenings out'),
(2, 'Sneakers', 'Casual and comfortable athletic shoes'),
(2, 'Boots', 'Sturdy footwear for various weather conditions'),
(2, 'Sandals', 'Open footwear for warm weather');

-- Insert Brands
INSERT INTO Brands (name, description) VALUES
('Nike', 'American multinational athletic wear'),
('Gucci', 'Italian luxury fashion house'),
('Adidas', 'German athletic wear and accessories'),
('Coach', 'American luxury accessories brand'),
('Clarks', 'British footwear manufacturer');

-- Insert Products
INSERT INTO Products (category_id, type_id, brand_id, gender_id, name, slug, description, price, stock_quantity) VALUES
(1, 1, 1, 3, 'Nike Sportswear RPM Backpack', 'nike-sportswear-rpm-backpack', 'Spacious backpack for daily use', 89.99, 100),
(1, 2, 4, 2, 'Coach Willow Tote', 'coach-willow-tote', 'Elegant leather tote for women', 295.00, 50),
(1, 3, 2, 2, 'Gucci GG Marmont Clutch', 'gucci-gg-marmont-clutch', 'Luxurious evening clutch', 1980.00, 25),
(2, 4, 3, 1, 'Adidas Ultraboost 21', 'adidas-ultraboost-21', 'High-performance running shoes', 180.00, 200),
(2, 5, 5, 2, 'Clarks Clarkdale Arlo Boot', 'clarks-clarkdale-arlo-boot', 'Comfortable leather boots for women', 160.00, 75),
(2, 6, 1, 3, 'Nike Benassi Slides', 'nike-benassi-slides', 'Casual and comfortable slides', 25.00, 300);

-- Insert Sizes
INSERT INTO Sizes (size_value) VALUES
('S'), ('M'), ('L'), ('XL'),  -- For bags
('6'), ('7'), ('8'), ('9'), ('10'), ('11');  -- For shoes

-- Insert Colors
INSERT INTO Colors (color_name) VALUES
('Black'), ('White'), ('Brown'), ('Blue'), ('Red');

-- Insert Product_Attributes
-- For bags (using S, M, L, XL as sizes)
INSERT INTO Product_Attributes (product_id, size_id, color_id, stock_quantity) VALUES
(1, 1, 1, 25), (1, 2, 1, 25), (1, 3, 1, 25), (1, 4, 1, 25),
(2, 2, 3, 25), (2, 3, 3, 25),
(3, 1, 2, 10), (3, 1, 5, 15);

-- For shoes (using numeric sizes)
INSERT INTO Product_Attributes (product_id, size_id, color_id, stock_quantity) VALUES
(4, 5, 4, 40), (4, 6, 4, 40), (4, 7, 4, 40), (4, 8, 4, 40), (4, 9, 4, 40),
(5, 5, 3, 15), (5, 6, 3, 15), (5, 7, 3, 15), (5, 8, 3, 15), (5, 9, 3, 15),
(6, 5, 1, 50), (6, 6, 1, 50), (6, 7, 1, 50), (6, 8, 1, 50), (6, 9, 1, 50), (6, 10, 1, 50);



-- Insert Product Photos
INSERT INTO Product_Photos (product_id, photo_url, is_primary) VALUES
-- Nike Sportswear RPM Backpack
(1, 'media/images/product/nike-rpm-backpack-1.jpg', TRUE),
(1, 'media/images/product/nike-rpm-backpack-2.jpg', FALSE),
(1, 'media/images/product/nike-rpm-backpack-3.jpg', FALSE),

-- Coach Willow Tote
(2, 'media/images/product/coach-willow-tote-1.jpg', TRUE),
(2, 'media/images/product/coach-willow-tote-2.jpg', FALSE),
(2, 'media/images/product/coach-willow-tote-3.jpg', FALSE),

-- Gucci GG Marmont Clutch
(3, 'media/images/product/gucci-marmont-clutch-1.jpg', TRUE),
(3, 'media/images/product/gucci-marmont-clutch-2.jpg', FALSE),
(3, 'media/images/product/gucci-marmont-clutch-3.jpg', FALSE),

-- Adidas Ultraboost 21
(4, 'media/images/product/adidas-ultraboost-21-1.jpg', TRUE),
(4, 'media/images/product/adidas-ultraboost-21-2.jpg', FALSE),
(4, 'media/images/product/adidas-ultraboost-21-3.jpg', FALSE),

-- Clarks Clarkdale Arlo Boot
(5, 'media/images/product/clarks-arlo-boot-1.jpg', TRUE),
(5, 'media/images/product/clarks-arlo-boot-2.jpg', FALSE),
(5, 'media/images/product/clarks-arlo-boot-3.jpg', FALSE),

-- Nike Benassi Slides
(6, 'media/images/product/nike-benassi-slides-1.jpg', TRUE),
(6, 'media/images/product/nike-benassi-slides-2.jpg', FALSE),
(6, 'media/images/product/nike-benassi-slides-3.jpg', FALSE);
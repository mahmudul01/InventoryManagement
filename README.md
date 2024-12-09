# InventoryManagement

## Create Database:

`
CREATE DATABASE IF NOT EXISTS inventorymanagement;
`
### Create all tables:

```
-- Create customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL
);

-- Create suppliers table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL
);

-- Create warehouses table
CREATE TABLE IF NOT EXISTS warehouses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    supplier_id INT NOT NULL,
    purchasing_price DECIMAL(10, 2) NOT NULL,
    selling_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- Create stocks table
CREATE TABLE IF NOT EXISTS stocks (
    stock_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
);

-- Create purchases table
CREATE TABLE IF NOT EXISTS purchases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) NOT NULL,
    supplier_id INT NOT NULL,
    purchase_date DATE NOT NULL,
    product_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
);

-- Create sales table
CREATE TABLE IF NOT EXISTS sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) NOT NULL,
    customer_id INT NOT NULL,
    purchase_date DATE NOT NULL,
    product_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
);

```

### Insert seed data for all tables
```
-- Insert into customers
INSERT INTO customers (name, email, phone, address)
VALUES 
    ('maryam savage', 'savacusy@mailinator.com', '+1 (972) 706-86', 'sunt animi hic sin asdf'),
    ('amity santiago', 'curanavami@mailinator.com', '+1 (395) 166-66', 'excepturi anim beata asdf'),
    ('claudia richmond', 'larugu@mailinator.com', '+1 (672) 926-40', 'consequatur enim te'),
    ('ronan gilmore', 'rapuga@mailinator.com', '+1 (414) 103-51', 'nulla incidunt magn'),
    ('bevis phillips', 'tytebozo@mailinator.com', '+1 (809) 783-71', 'et minim enim corpor');

-- Insert into suppliers
INSERT INTO suppliers (name, email, phone, address)
VALUES 
    ('tech supply inc.', 'techsupply@mailinator.com', '+1 (702) 123-45', 'modern devices supplier'),
    ('gadget pro', 'gadgetpro@mailinator.com', '+1 (805) 678-90', 'high-quality electronics'),
    ('device world', 'deviceworld@mailinator.com', '+1 (212) 345-67', 'all types of devices supplier'),
    ('smart tech', 'smarttech@mailinator.com', '+1 (654) 321-09', 'innovative technology supplier'),
    ('future gadgets', 'futuregadgets@mailinator.com', '+1 (987) 654-32', 'latest gadgets and devices');

-- Insert into warehouses
INSERT INTO warehouses (name, location, phone)
VALUES 
    ('central warehouse', 'new york', '123-456-7890'),
    ('east warehouse', 'boston', '234-567-8901'),
    ('west warehouse', 'los angeles', '345-678-9012');

-- Insert into products
INSERT INTO products (name, description, supplier_id, purchasing_price, selling_price)
VALUES 
    ('laptop', 'high performance laptop', 1, 800.00, 1000.00),
    ('smartphone', 'latest model smartphone', 2, 500.00, 700.00),
    ('tablet', 'lightweight tablet', 3, 300.00, 450.00);

-- Insert into stocks
INSERT INTO stocks (product_id, warehouse_id, quantity)
VALUES 
    (1, 1, 50),  -- Laptop in central warehouse
    (2, 2, 30),  -- Smartphone in east warehouse
    (3, 3, 20);  -- Tablet in west warehouse

-- Insert into purchases
INSERT INTO purchases (invoice_number, supplier_id, purchase_date, product_id, warehouse_id, total_amount)
VALUES 
    ('inv-001', 1, '2024-12-01', 1, 1, 40000.00), -- Laptop purchase
    ('inv-002', 2, '2024-12-02', 2, 2, 15000.00), -- Smartphone purchase
    ('inv-003', 3, '2024-12-03', 3, 3, 6000.00);  -- Tablet purchase

-- Insert into sales
INSERT INTO sales (invoice_number, customer_id, purchase_date, product_id, warehouse_id, total_amount)
VALUES 
    ('sal-001', 11, '2024-12-05', 1, 1, 5000.00), -- Laptop sale
    ('sal-002', 12, '2024-12-06', 2, 2, 2100.00), -- Smartphone sale
    ('sal-003', 13, '2024-12-07', 3, 3, 900.00);  -- Tablet sale

```


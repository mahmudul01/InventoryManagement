# InventoryManagement

## Create Database:

`
CREATE DATABASE IF NOT EXISTS inventorymanagement;
`
### Create customers table:

```
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL
);
```

### Insert a test data
```
INSERT INTO customers (name, email, phone, address)
VALUES ('John Doe', 'john.doe@example.com', '123-456-7890', '123 Main Street, Cityville');
```

### Create suppliers table:

```
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL
);
```

### Insert a test data
```
INSERT INTO suppliers (name, email, phone, address)
VALUES ('Shohag Ahmed', 'shohag@example.com', '543-456-7890', '123 Md Pur, Cityville');
```

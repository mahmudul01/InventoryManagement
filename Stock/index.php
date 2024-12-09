<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventorymanagement";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch stocks with product and warehouse names
$sql = "
    SELECT 
        stocks.stock_id, 
        products.name AS product_name, 
        warehouses.name AS warehouse_name, 
        stocks.quantity 
    FROM 
        stocks
    INNER JOIN 
        products ON stocks.product_id = products.id
    INNER JOIN 
        warehouses ON stocks.warehouse_id = warehouses.id
";
$result = $conn->query($sql);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2>Stock List</h2>        
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Stock ID</th>
                <th>Product Name</th>
                <th>Warehouse Name</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['stock_id']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['warehouse_name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No stock items found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

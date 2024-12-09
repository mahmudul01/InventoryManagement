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

// Fetch purchases from the database
$sql = "
    SELECT 
        purchases.id,
        purchases.invoice_number,
        suppliers.name AS supplier_name,
        products.name AS product_name,
        warehouses.name AS warehouse_name,
        purchases.purchase_date,
        purchases.quantity,
        purchases.total_amount
    FROM purchases
    INNER JOIN suppliers ON purchases.supplier_id = suppliers.id
    INNER JOIN products ON purchases.product_id = products.id
    INNER JOIN warehouses ON purchases.warehouse_id = warehouses.id
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
    <title>Purchase List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-6">
            <h2>Purchase List</h2>        
        </div>
        <div class="col-6 text-end">
            <a href="create_purchase.php" class="btn btn-primary mb-3">Create Purchase</a>        
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Invoice Number</th>
                <th>Supplier</th>
                <th>Product</th>
                <th>Warehouse</th>
                <th>Purchase Date</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['invoice_number']; ?></td>
                        <td><?php echo $row['supplier_name']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['warehouse_name']; ?></td>
                        <td><?php echo $row['purchase_date']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['total_amount']; ?></td>
                        <td>
                            <a href="edit_purchase.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_purchase.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this purchase?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No purchases found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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

// Get product ID from the query string
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

if (!$product) {
    echo "Product not found.";
    exit;
}

// Fetch suppliers from the database
$sql = "SELECT id, name FROM suppliers";
$suppliers = $conn->query($sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $supplier_id = $_POST['supplier_id'];
    $purchasing_price = $_POST['purchasing_price'];
    $selling_price = $_POST['selling_price'];

    // Update product in the database
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, supplier_id = ?, purchasing_price = ?, selling_price = ? WHERE id = ?");
    $stmt->bind_param("ssiddi", $name, $description, $supplier_id, $purchasing_price, $selling_price, $product_id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Product</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select class="form-select" id="supplier_id" name="supplier_id" required>
                <option value="" disabled>Select Supplier</option>
                <?php if ($suppliers && $suppliers->num_rows > 0): ?>
                    <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                        <option value="<?php echo $supplier['id']; ?>" <?php echo $supplier['id'] == $product['supplier_id'] ? 'selected' : ''; ?>><?php echo $supplier['name']; ?></option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="" disabled>No suppliers available</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="purchasing_price" class="form-label">Purchasing Price</label>
            <input type="number" step="0.01" class="form-control" id="purchasing_price" name="purchasing_price" value="<?php echo htmlspecialchars($product['purchasing_price']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="selling_price" class="form-label">Selling Price</label>
            <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" value="<?php echo htmlspecialchars($product['selling_price']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
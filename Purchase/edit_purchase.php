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

// Fetch warehouses for the dropdown
$warehouses = $conn->query("SELECT id, name FROM warehouses");

// Fetch all products for the dropdown
$products = $conn->query("SELECT id, name, purchasing_price FROM products");

// Fetch suppliers for the supplier dropdown
$suppliers = $conn->query("SELECT id, name FROM suppliers");

// Get the purchase id from GET request for editing
$purchase_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$purchase_data = null;

// Fetch existing data if editing
if ($purchase_id) {
    $stmt = $conn->prepare("SELECT * FROM purchases WHERE id = ?");
    $stmt->bind_param("i", $purchase_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $purchase_data = $result->fetch_assoc();
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = intval($_POST['supplier_id']);
    $warehouse_id = intval($_POST['warehouse_id']);
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $purchase_date = $_POST['purchase_date'];
    $total_amount = floatval($_POST['total_amount']);
    $purchase_id = intval($_POST['purchase_id']); // Ensure we get the ID for editing

    // Update purchase in database
    $stmt = $conn->prepare("UPDATE purchases SET supplier_id = ?, warehouse_id = ?, product_id = ?, quantity = ?, purchase_date = ?, total_amount = ? WHERE id = ?");
    $stmt->bind_param("iiidddi", $supplier_id, $warehouse_id, $product_id, $quantity, $purchase_date, $total_amount, $purchase_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Purchase updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
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
    <title>Edit Purchase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function calculateTotal() {
            const quantity = document.getElementById('quantity').value;
            const price = document.getElementById('product_id').selectedOptions[0].dataset.price || 0;
            const total = quantity * price;
            document.getElementById('total_amount').value = total.toFixed(2);
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Purchase</h2>
    <form method="POST">
        <input type="hidden" name="purchase_id" value="<?php echo $purchase_data ? $purchase_data['id'] : 0; ?>">

        <div class="mb-3">
            <label for="invoice_number" class="form-label">Invoice Number</label>
            <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $purchase_data ? $purchase_data['invoice_number'] : time(); ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="warehouse_id" class="form-label">Warehouse</label>
            <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                <option value="">Select Warehouse</option>
                <?php while ($row = $warehouses->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo $purchase_data && $purchase_data['warehouse_id'] == $row['id'] ? 'selected' : ''; ?>>
                        <?php echo $row['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="product_id" class="form-label">Product</label>
            <select class="form-select" id="product_id" name="product_id" onchange="calculateTotal()" required>
                <option value="">Select Product</option>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" data-price="<?php echo $row['purchasing_price']; ?>"
                        <?php echo $purchase_data && $purchase_data['product_id'] == $row['id'] ? 'selected' : ''; ?>>
                        <?php echo $row['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-select" required>
                <option value="">Select Supplier</option>
                <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                    <option value="<?php echo $supplier['id']; ?>" <?php echo $purchase_data && $purchase_data['supplier_id'] == $supplier['id'] ? 'selected' : ''; ?>>
                        <?php echo $supplier['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $purchase_data ? $purchase_data['quantity'] : ''; ?>" oninput="calculateTotal()" required>
        </div>

        <div class="mb-3">
            <label for="purchase_date" class="form-label">Purchase Date</label>
            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo $purchase_data ? $purchase_data['purchase_date'] : date('Y-m-d'); ?>" required>
        </div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Amount</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" value="<?php echo $purchase_data ? $purchase_data['total_amount'] : ''; ?>" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Update Purchase</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

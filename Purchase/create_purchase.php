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

// Generate invoice number
$invoice_number = "INV-" . time();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = intval($_POST['supplier_id']);
    $warehouse_id = intval($_POST['warehouse_id']);
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $purchase_date = $_POST['purchase_date'];
    $total_amount = floatval($_POST['total_amount']);
    $invoice_number = $_POST['invoice_number']; // Ensure this value is assigned properly

    // Insert purchase into purchases table
    $stmt = $conn->prepare("INSERT INTO purchases (invoice_number, supplier_id, purchase_date, product_id, warehouse_id, quantity, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Correctly bind parameters with proper types
    $stmt->bind_param("sisddii", $invoice_number, $supplier_id, $purchase_date, $product_id, $warehouse_id, $quantity, $total_amount);

    if ($stmt->execute()) {
        // Check if the product_id and warehouse_id combination exists in the stocks table
        $check_stock_stmt = $conn->prepare("SELECT quantity FROM stocks WHERE product_id = ? AND warehouse_id = ?");
        $check_stock_stmt->bind_param("ii", $product_id, $warehouse_id);
        $check_stock_stmt->execute();
        $result = $check_stock_stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing stock entry
            $update_stmt = $conn->prepare("UPDATE stocks SET quantity = quantity + ? WHERE product_id = ? AND warehouse_id = ?");
            $update_stmt->bind_param("iii", $quantity, $product_id, $warehouse_id);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            // Insert new entry into stocks
            $insert_stmt = $conn->prepare("INSERT INTO stocks (product_id, warehouse_id, quantity) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("iii", $product_id, $warehouse_id, $quantity);
            $insert_stmt->execute();
            $insert_stmt->close();
        }

        echo "<div class='alert alert-success'>Purchase added and stock updated successfully!</div>";
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
    <title>Create Purchase</title>
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
    <h2>Create Purchase</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="invoice_number" class="form-label">Invoice Number</label>
            <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $invoice_number; ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="warehouse_id" class="form-label">Warehouse</label>
            <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                <option value="">Select Warehouse</option>
                <?php while ($row = $warehouses->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="product_id" class="form-label">Product</label>
            <select class="form-select" id="product_id" name="product_id" onchange="calculateTotal()" required>
                <option value="">Select Product</option>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" data-price="<?php echo $row['purchasing_price']; ?>">
                        <?php echo $row['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-select" required>
                <option value="">Select Supplier</option>
                <?php
                $suppliers = $conn = new mysqli($servername, $username, $password, $dbname);
                $suppliers_result = $suppliers->query("SELECT id, name FROM suppliers");
                while ($supplier = $suppliers_result->fetch_assoc()) {
                    echo "<option value='{$supplier['id']}'>{$supplier['name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" oninput="calculateTotal()" required>
        </div>

        <div class="mb-3">
            <label for="purchase_date" class="form-label">Purchase Date</label>
            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Amount</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Create Purchase</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

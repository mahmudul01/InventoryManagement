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
$products = $conn->query("SELECT id, name, selling_price FROM products");

// Generate invoice number
$invoice_number = "SALE-" . time();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $warehouse_id = intval($_POST['warehouse_id']);
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $sale_date = $_POST['sale_date'];
    $total_amount = floatval($_POST['total_amount']);
    $invoice_number = $_POST['invoice_number']; // Ensure this value is assigned properly

    // Check stock availability
    $stock_stmt = $conn->prepare("SELECT quantity FROM stocks WHERE product_id = ? AND warehouse_id = ?");
    $stock_stmt->bind_param("ii", $product_id, $warehouse_id);
    $stock_stmt->execute();
    $stock_result = $stock_stmt->get_result();

    if ($stock_result->num_rows > 0) {
        $stock_row = $stock_result->fetch_assoc();
        if ($stock_row['quantity'] >= $quantity) {
            // Insert sale into sales table
            $stmt = $conn->prepare("INSERT INTO sales (invoice_number, customer_id, sale_date, product_id, warehouse_id, quantity, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");

            // Bind parameters
            $stmt->bind_param("sisddii", $invoice_number, $customer_id, $sale_date, $product_id, $warehouse_id, $quantity, $total_amount);

            if ($stmt->execute()) {
                // Update stock quantity
                $update_stock_stmt = $conn->prepare("UPDATE stocks SET quantity = quantity - ? WHERE product_id = ? AND warehouse_id = ?");
                $update_stock_stmt->bind_param("iii", $quantity, $product_id, $warehouse_id);
                $update_stock_stmt->execute();
                $update_stock_stmt->close();

                echo "<div class='alert alert-success'>Sale recorded and stock updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }

            $stmt->close();
        } else {
            echo "<div class='alert alert-warning'>Insufficient stock for this product in the selected warehouse.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Product not available in the selected warehouse.</div>";
    }

    $stock_stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Sale</title>
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
    <h2>Create Sale</h2>
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
                    <option value="<?php echo $row['id']; ?>" data-price="<?php echo $row['selling_price']; ?>">
                        <?php echo $row['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                <option value="">Select Customer</option>
                <?php
                $customers = $conn = new mysqli($servername, $username, $password, $dbname);
                $customers_result = $customers->query("SELECT id, name FROM customers");
                while ($customer = $customers_result->fetch_assoc()) {
                    echo "<option value='{$customer['id']}'>{$customer['name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" oninput="calculateTotal()" required>
        </div>

        <div class="mb-3">
            <label for="sale_date" class="form-label">Sale Date</label>
            <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Amount</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Create Sale</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

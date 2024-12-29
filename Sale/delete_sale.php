<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventorymanagement";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get sale ID from the GET request
$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($sale_id > 0) {
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Step 1: Retrieve sale details
        $stmt = $conn->prepare("SELECT product_id, warehouse_id, quantity FROM sales WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error preparing fetch sale query: " . $conn->error);
        }
        $stmt->bind_param("i", $sale_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Sale not found for the provided ID.");
        }

        $sale = $result->fetch_assoc();
        $product_id = $sale['product_id'];
        $warehouse_id = $sale['warehouse_id'];
        $quantity = $sale['quantity'];

        $stmt->close();

        // Step 2: Update stock
        $update_stock_stmt = $conn->prepare("
            UPDATE stocks
            SET quantity = quantity + ?
            WHERE product_id = ? AND warehouse_id = ?
        ");
        if (!$update_stock_stmt) {
            throw new Exception("Error preparing stock update query: " . $conn->error);
        }
        $update_stock_stmt->bind_param("iii", $quantity, $product_id, $warehouse_id);

        if (!$update_stock_stmt->execute()) {
            throw new Exception("Error executing stock update query: " . $update_stock_stmt->error);
        }

        $update_stock_stmt->close();

        // Step 3: Delete the sale
        $delete_sale_stmt = $conn->prepare("DELETE FROM sales WHERE id = ?");
        if (!$delete_sale_stmt) {
            throw new Exception("Error preparing sale delete query: " . $conn->error);
        }
        $delete_sale_stmt->bind_param("i", $sale_id);

        if (!$delete_sale_stmt->execute()) {
            throw new Exception("Error executing sale delete query: " . $delete_sale_stmt->error);
        }

        $delete_sale_stmt->close();

        // Commit the transaction
        $conn->commit();
        echo "<div class='alert alert-success'>Sale deleted and stock updated successfully!</div>";
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Invalid sale ID!</div>";
}

// Close the connection
$conn->close();
?>

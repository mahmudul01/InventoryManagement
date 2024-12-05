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

// Check if 'id' is passed in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $warehouse_id = $_GET['id'];

    // Prepare and execute the delete query
    $sql = "DELETE FROM warehouses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $warehouse_id);

    if ($stmt->execute()) {
        // Redirect to the customer list page after deletion
        header("Location: index.php?message=Warehouse Deleted Successfully");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error deleting warehouse: " . $conn->error . "</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Invalid warehouse ID.</div>";
}

// Close the connection
$conn->close();
?>
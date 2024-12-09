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
    $customer_id = $_GET['id'];

    // Prepare and execute the delete query
    $sql = "DELETE FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);

    if ($stmt->execute()) {
        // Redirect to the customer list page after deletion
        header("Location: index.php?message=Customer Deleted Successfully");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error deleting customer: " . $conn->error . "</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Invalid customer ID.</div>";
}

// Close the connection
$conn->close();
?>

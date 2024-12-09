<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventorymanagement";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['warehouse_id'])) {
    $warehouse_id = intval($_GET['warehouse_id']);
    $query = $conn->prepare("
        SELECT p.id, p.name, s.quantity 
        FROM products p 
        INNER JOIN stocks s ON p.id = s.product_id 
        WHERE s.warehouse_id = ?
    ");
    $query->bind_param("i", $warehouse_id);
    $query->execute();
    $result = $query->get_result();

    echo '<option value="">Select Product</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '">' . $row['name'] . ' (' . $row['quantity'] . ')</option>';
    }

    $query->close();
}

$conn->close();
?>

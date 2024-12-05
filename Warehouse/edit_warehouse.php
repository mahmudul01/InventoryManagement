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

// Initialize the ID variable
$id = null;

// Get the warehouse ID from the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch warehouse data based on the ID
    $sql = "SELECT * FROM warehouses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $warehouse = $result->fetch_assoc();
    } else {
        echo "Warehouse not found!";
        exit;
    }
}

// PHP code to handle form submission for updating warehouse data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $id !== null) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Update the warehouse data in the database
    $sql = "UPDATE warehouses SET name = ?, address = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $address, $phone, $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'> Warehouse Updated Successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
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
    <title>Edit Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-6">
            <h2>Edit Warehouse</h2>
        </div>
        <div class="col-6 text-end">
            <a href="index.php" class="btn btn-success mb-3">Back to list</a>
        </div>
    </div>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($warehouse['name'], ENT_QUOTES); ?>" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address" required><?php echo htmlspecialchars($warehouse['address'], ENT_QUOTES); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($warehouse['phone'], ENT_QUOTES); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Warehouse</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

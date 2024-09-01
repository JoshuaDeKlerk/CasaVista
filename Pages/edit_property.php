<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$property_id = isset($_GET['id']) ? $_GET['id'] : null;
$email = $_SESSION["user"];

if ($property_id) {
    // Fetch the property details for editing
    $sql = "SELECT * FROM property_requests WHERE id = ? AND agent_email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "is", $property_id, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $property = mysqli_fetch_assoc($result);
        
        if (!$property) {
            echo "Property not found or you do not have permission to edit it.";
            exit();
        }
    }

    // Handle form submission to update property
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $property_name = $_POST['property_name'];
        $property_description = $_POST['property_description'];
        $property_price = $_POST['property_price'];
        $year_built = $_POST['year_built'];
        $property_status = $_POST['property_status'];
        $bedrooms = $_POST['bedrooms'];
        $bathrooms = $_POST['bathrooms'];

        $update_sql = "UPDATE property_requests SET property_name = ?, property_description = ?, property_price = ?, year_built = ?, property_status = ?, bedrooms = ?, bathrooms = ? WHERE id = ?";
        $update_stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($update_stmt, $update_sql)) {
            mysqli_stmt_bind_param($update_stmt, "sssssssi", $property_name, $property_description, $property_price, $year_built, $property_status, $bedrooms, $bathrooms, $property_id);
            mysqli_stmt_execute($update_stmt);
            header("Location: properties.php");
            exit();
        } else {
            echo "Failed to update property.";
        }
    }
} else {
    echo "Invalid property ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="container mt-5">
        <h2>Edit Property</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="property_name" class="form-label">Property Name</label>
                <input type="text" name="property_name" id="property_name" class="form-control" value="<?php echo htmlspecialchars($property['property_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="property_description" class="form-label">Property Description</label>
                <textarea name="property_description" id="property_description" class="form-control" rows="3" required><?php echo htmlspecialchars($property['property_description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="property_price" class="form-label">Property Price</label>
                <input type="number" name="property_price" id="property_price" class="form-control" value="<?php echo htmlspecialchars($property['property_price']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="year_built" class="form-label">Year Built</label>
                <input type="number" name="year_built" id="year_built" class="form-control" value="<?php echo htmlspecialchars($property['year_built']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="property_status" class="form-label">Property Status</label>
                <select name="property_status" id="property_status" class="form-control" required>
                    <option value="For Sale" <?php echo $property['property_status'] == 'For Sale' ? 'selected' : ''; ?>>For Sale</option>
                    <option value="For Rent" <?php echo $property['property_status'] == 'For Rent' ? 'selected' : ''; ?>>For Rent</option>
                    <option value="Under Contract" <?php echo $property['property_status'] == 'Under Contract' ? 'selected' : ''; ?>>Under Contract</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="bedrooms" class="form-label">Bedrooms</label>
                <input type="number" name="bedrooms" id="bedrooms" class="form-control" value="<?php echo htmlspecialchars($property['bedrooms']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="bathrooms" class="form-label">Bathrooms</label>
                <input type="number" name="bathrooms" id="bathrooms" class="form-control" value="<?php echo htmlspecialchars($property['bathrooms']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Property</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

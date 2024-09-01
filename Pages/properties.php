<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$email = $_SESSION["user"];

// Fetch all properties added by the logged-in agent/admin
$properties = [];
$sql = "SELECT * FROM property_requests WHERE agent_email = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($property = mysqli_fetch_assoc($result)) {
        $properties[] = $property;
    }
}

// Handle property deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_property'])) {
    $property_id = $_POST['property_id'];
    $delete_sql = "DELETE FROM property_requests WHERE id = ?";
    $delete_stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($delete_stmt, $delete_sql)) {
        mysqli_stmt_bind_param($delete_stmt, "i", $property_id);
        mysqli_stmt_execute($delete_stmt);
        // Refresh the page to update the list of properties
        header("Location: properties.php");
        exit();
    } else {
        echo "Failed to delete the property.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Properties</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CasaVista/Style/properties.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="container mt-5">
        <h2>Your Properties</h2>
        <?php if (count($properties) > 0): ?>
            <ul class="list-group">
                <?php foreach ($properties as $property): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5><?php echo htmlspecialchars($property['property_name']); ?></h5>
                            <p><?php echo htmlspecialchars($property['property_description']); ?></p>
                            <p><strong>Price:</strong> R<?php echo number_format($property['property_price'], 2); ?></p>
                            <p><strong>Year Built:</strong> <?php echo htmlspecialchars($property['year_built']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($property['property_status']); ?></p>
                            <p><strong>Bedrooms:</strong> <?php echo htmlspecialchars($property['bedrooms']); ?></p>
                            <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?></p>
                        </div>
                        <div>
                            <a href="/CasaVista/Pages/edit_property.php?id=<?php echo $property['id']; ?>" class="btn btn-warning mb-2">Edit</a>
                            <form action="properties.php" method="POST" class="d-inline-block">
                                <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                                <button type="submit" name="delete_property" class="btn btn-danger">Remove</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You have not added any properties yet.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$email = $_SESSION["user"];

// Check if the user is an admin
$sql = "SELECT user_type FROM user WHERE email = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $user_type = $user['user_type'];

    if ($user_type !== 'admin') {
        echo "You do not have permission to access this page.";
        exit();
    }
}

// Handle approval or rejection of property requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = $_POST['property_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $sql = "UPDATE property_requests SET approved = TRUE WHERE id = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $property_id);
            mysqli_stmt_execute($stmt);
            echo "Property approved.";
        }
    } elseif ($action === 'reject') {
        $sql = "DELETE FROM property_requests WHERE id = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $property_id);
            mysqli_stmt_execute($stmt);
            echo "Property rejected.";
        }
    }
}

// Fetch all pending property requests
$property_requests = [];
$sql = "SELECT * FROM property_requests WHERE approved = FALSE";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($property = mysqli_fetch_assoc($result)) {
        // Fetch property images
        $property['images'] = [];
        $image_sql = "SELECT image_link FROM property_images WHERE property_id = ?";
        $image_stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($image_stmt, $image_sql)) {
            mysqli_stmt_bind_param($image_stmt, "i", $property['id']);
            mysqli_stmt_execute($image_stmt);
            $image_result = mysqli_stmt_get_result($image_stmt);
            while ($image = mysqli_fetch_assoc($image_result)) {
                $property['images'][] = $image['image_link'];
            }
        }

        $property_requests[] = $property;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Property Approval</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CasaVista/Style/admin.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="AdminContainer" style="    width: 100%;
    background-color: #393939;
    padding: 20px 100px;
    min-height: 100vh;">
        <h2>Pending Property Approvals</h2>
        <?php if (count($property_requests) > 0): ?>
            <ul class="list-group">
                <?php foreach ($property_requests as $property): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="property-info d-flex align-items-center">
                            <img src="<?php echo htmlspecialchars($property['images'][0]); ?>" alt="Property Image" class="property-image" style="width: 150px; height: auto;">
                            <div class="ms-3">
                                <h5><?php echo htmlspecialchars($property['property_name']); ?></h5>
                                <p><?php echo htmlspecialchars($property['property_price']); ?> ZAR</p>
                            </div>
                        </div>
                        <div class="btn-group">
                            <a href="/CasaVista/Pages/property.php?id=<?php echo $property['id']; ?>" class="btn btn-info btn-sm">View</a>
                            <form action="/CasaVista/Pages/admin.php" method="POST" class="d-inline-block">
                                <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No pending property requests.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

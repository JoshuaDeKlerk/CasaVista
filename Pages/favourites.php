<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$email = $_SESSION["user"];

// Fetch the user's favourite properties
$favourites = [];
$sql = "SELECT p.*, i.image_link FROM favourites f
        JOIN property_requests p ON f.property_id = p.id
        LEFT JOIN (SELECT property_id, MIN(image_link) as image_link FROM property_images GROUP BY property_id) i 
        ON p.id = i.property_id
        WHERE f.user_email = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($property = mysqli_fetch_assoc($result)) {
        $favourites[] = $property;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favourites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="container mt-5">
        <h2>My Favourites</h2>
        <div class="row">
            <?php if (count($favourites) > 0): ?>
                <?php foreach ($favourites as $property): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <?php if (!empty($property['image_link'])): ?>
                                <img src="<?php echo htmlspecialchars($property['image_link']); ?>" class="card-img-top" alt="Property Image">
                            <?php else: ?>
                                <img src="path_to_default_image.jpg" class="card-img-top" alt="Default Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($property['property_name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($property['property_description']); ?></p>
                                <p class="card-text">Price: R<?php echo number_format($property['property_price'], 2); ?></p>
                                <p class="card-text">Year Built: <?php echo htmlspecialchars($property['year_built']); ?></p>
                                <p class="card-text">Bedrooms: <?php echo htmlspecialchars($property['bedrooms']); ?></p>
                                <p class="card-text">Bathrooms: <?php echo htmlspecialchars($property['bathrooms']); ?></p>
                                <a href="property.php?id=<?php echo $property['id']; ?>" class="btn btn-primary">View Property</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no favourite properties yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
    <link rel="stylesheet" href="/CasaVista/Style/browse.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="BrowseContainer">
        <div class="BrowseTitle">
            <h1>My Favourites</h1>
        </div>
        <div class="CardContainer">
            <div class="row">
                <?php if (count($favourites) > 0): ?>
                    <?php foreach ($favourites as $property): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="PropertyCard mb-4">
                                <?php if (!empty($property['image_link'])): ?>
                                    <img src="<?php echo htmlspecialchars($property['image_link']); ?>" class="PropertyImage d-block w-100" alt="Property Image">
                                <?php else: ?>
                                    <img src="path_to_default_image.jpg" class="PropertyImage d-block w-100" alt="Default Image">
                                <?php endif; ?>
                                <div class="PropertyDetails card-body">
                                    <h5 class="PropertyTitle card-title"><?php echo htmlspecialchars($property['property_name']); ?></h5>
                                    <p class="PropertyType card-text">
                                        <span>Type: <?php echo htmlspecialchars($property['property_type']); ?></span><br>
                                        <span>Status: <?php echo htmlspecialchars($property['property_status']); ?></span>
                                    </p>
                                    <p class="PropertyFeatures card-text">
                                        <span>Bathrooms: <?php echo htmlspecialchars($property['bathrooms']); ?></span><br>
                                        <span>Bedrooms: <?php echo htmlspecialchars($property['bedrooms']); ?></span>
                                    </p>
                                    <p class="PropertyPrice card-text">R<?php echo number_format($property['property_price'], 2); ?></p>
                                    <p class="YearBuilt card-text">Year Built: <?php echo htmlspecialchars($property['year_built']); ?></p>
                                    <a href="property.php?id=<?php echo $property['id']; ?>" class="PropertyButton">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You have no favourite properties yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

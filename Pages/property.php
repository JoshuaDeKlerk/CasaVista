<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$email = $_SESSION["user"];

// Get the property ID from the query string
$property_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($property_id) {
    // Fetch the property details
    $sql = "SELECT * FROM property_requests WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $property_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $property = mysqli_fetch_assoc($result);
        
        if ($property) {
            // Fetch property images
            $images = [];
            $image_sql = "SELECT image_link FROM property_images WHERE property_id = ?";
            $image_stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($image_stmt, $image_sql)) {
                mysqli_stmt_bind_param($image_stmt, "i", $property_id);
                mysqli_stmt_execute($image_stmt);
                $image_result = mysqli_stmt_get_result($image_stmt);
                while ($image = mysqli_fetch_assoc($image_result)) {
                    $images[] = $image['image_link'];
                }
            }

            // Fetch agent details
            $agent_email = $property['agent_email'];
            $agent_sql = "SELECT full_name, profile_picture FROM user WHERE email = ?";
            $agent_stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($agent_stmt, $agent_sql)) {
                mysqli_stmt_bind_param($agent_stmt, "s", $agent_email);
                mysqli_stmt_execute($agent_stmt);
                $agent_result = mysqli_stmt_get_result($agent_stmt);
                $agent = mysqli_fetch_assoc($agent_result);
            }

            // Fetch property reviews
            $reviews = [];
            $review_sql = "SELECT * FROM property_reviews WHERE property_id = ?";
            $review_stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($review_stmt, $review_sql)) {
                mysqli_stmt_bind_param($review_stmt, "i", $property_id);
                mysqli_stmt_execute($review_stmt);
                $review_result = mysqli_stmt_get_result($review_stmt);
                while ($review = mysqli_fetch_assoc($review_result)) {
                    $reviews[] = $review;
                }
            }

            // Handle review submission
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
                $review_text = $_POST['review_text'];
                $reviewer_name = $_SESSION['user'];
                $review_sql = "INSERT INTO property_reviews (property_id, reviewer_name, review_text, review_date) VALUES (?, ?, ?, NOW())";
                $review_stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($review_stmt, $review_sql)) {
                    mysqli_stmt_bind_param($review_stmt, "iss", $property_id, $reviewer_name, $review_text);
                    mysqli_stmt_execute($review_stmt);
                    // Refresh page to show the new review
                    header("Location: property.php?id=$property_id");
                    exit();
                }
            }

            // Handle adding property to favorites
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_favorites'])) {
                $favourites_sql = "INSERT INTO favourites (user_email, property_id) VALUES (?, ?)";
                $favourites_stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($favourites_stmt, $favourites_sql)) {
                    mysqli_stmt_bind_param($favourites_stmt, "si", $email, $property_id);
                    mysqli_stmt_execute($favourites_stmt);
                    // Redirect to the favourites page
                    header("Location: /CasaVista/Pages/favourites.php");
                    exit();
                } else {
                    echo "Error adding to favourites.";
                }
            }

            // Handle adding property to cart
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
                $cart_sql = "INSERT INTO cart (user_email, property_id) VALUES (?, ?)";
                $cart_stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($cart_stmt, $cart_sql)) {
                    mysqli_stmt_bind_param($cart_stmt, "si", $email, $property_id);
                    mysqli_stmt_execute($cart_stmt);
                    // Redirect to the cart page
                    header("Location: /CasaVista/Pages/cart.php");
                    exit();
                } else {
                    echo "Error adding to cart.";
                }
            }
        } else {
            echo "Property not found.";
            exit();
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
    <title><?php echo htmlspecialchars($property['property_name']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CasaVista/Style/property.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <!-- Main Image -->
                <div class="main-image mb-4">
                    <?php if (!empty($images[0])): ?>
                        <img src="<?php echo htmlspecialchars($images[0]); ?>" alt="Main Property Image" class="img-fluid">
                    <?php else: ?>
                        <div class="placeholder">Main Image</div>
                    <?php endif; ?>
                </div>
                
                <!-- Thumbnail Images -->
                <div class="row">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="col-3 thumbnail">
                            <?php if (!empty($images[$i])): ?>
                                <img src="<?php echo htmlspecialchars($images[$i]); ?>" alt="Property Image <?php echo $i + 1; ?>" class="thumbnail img-fluid">
                            <?php else: ?>
                                <div class="thumbnail-placeholder">Image <?php echo $i + 1; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="property-details">
                    <h3><?php echo htmlspecialchars($property['property_name']); ?></h3>
                    <h5><?php echo htmlspecialchars($property['property_status']); ?></h5>
                    <p class="price">R<?php echo number_format($property['property_price'], 2); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($property['property_description']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($property['property_type']); ?></p>
                    <div class="size-details">
                        <p><strong>Total Area:</strong> <?php echo htmlspecialchars($property['total_area']); ?> sq ft</p>
                        <p><strong>Living Room Size:</strong> <?php echo htmlspecialchars($property['living_room_size']); ?> sq ft</p>
                        <p><strong>Kitchen Size:</strong> <?php echo htmlspecialchars($property['kitchen_size']); ?> sq ft</p>
                        <p><strong>Bedrooms:</strong> <?php echo htmlspecialchars($property['bedrooms']); ?></p>
                        <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?></p>
                    </div>
                    <p><strong>Condition:</strong> <?php echo htmlspecialchars($property['property_condition']); ?></p>
                    <p><strong>Year Built:</strong> <?php echo htmlspecialchars($property['year_built']); ?></p>
                    <p><strong>Furnishing:</strong> <?php echo htmlspecialchars($property['furnishing']); ?></p>
                    <div class="map-location mb-3">
                        <?php echo $property['map_location']; ?>
                    </div>
                    <p><strong>Street and City:</strong> <?php echo htmlspecialchars($property['street_city']); ?></p>
                    <p><strong>State/Province:</strong> <?php echo htmlspecialchars($property['state_province']); ?></p>
                    <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($property['postal_code']); ?></p>
                    <p><strong>Available From:</strong> <?php echo htmlspecialchars($property['available_from']); ?></p>
                    <p><strong>Pet Policy:</strong> <?php echo htmlspecialchars($property['pet_policy']); ?></p>
                    <p><strong>Utilities Included:</strong> <?php echo htmlspecialchars($property['utilities_included']); ?></p>
                    <!-- Add to Cart and Favorites Form -->
                    <form action="" method="POST">
                        <button type="submit" name="add_to_cart" class="btn btn-secondary mb-2">Add to Cart</button>
                        <button type="submit" name="add_to_favorites" class="btn btn-primary">Favourite</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Agent Card -->
        <div class="agent-card mt-5 p-3">
            <h4>Agent Information</h4>
            <div class="d-flex align-items-center">
                <div class="agent-picture">
                    <img src="<?php echo htmlspecialchars('/CasaVista' . $agent['profile_picture']); ?>" alt="Agent Picture" class="img-fluid rounded-circle" style="width: 100px; height: 100px;">
                </div>
                <div class="ms-3">
                    <h5><?php echo htmlspecialchars($agent['full_name']); ?></h5>
                    <a href="profile.php?email=<?php echo urlencode($agent_email); ?>" class="btn btn-info">View Profile</a>
                </div>
            </div>
        </div>

        <!-- Property Reviews -->
        <div class="property-reviews mt-5">
            <h4>Property Reviews</h4>
            <?php if (count($reviews) > 0): ?>
                <ul class="list-group mb-4">
                    <?php foreach ($reviews as $review): ?>
                        <li class="list-group-item">
                            <strong><?php echo htmlspecialchars($review['reviewer_name']); ?>:</strong>
                            <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <small class="text-muted"><?php echo htmlspecialchars($review['review_date']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No reviews available for this property.</p>
            <?php endif; ?>
            <!-- Review Form -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="review_text" class="form-label">Leave a Review</label>
                    <textarea name="review_text" id="review_text" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

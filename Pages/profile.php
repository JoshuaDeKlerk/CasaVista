<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

// Get agent's email from query string
$agent_email = isset($_GET['email']) ? $_GET['email'] : null;

if ($agent_email) {
    // Fetch agent details
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $agent_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $agent = mysqli_fetch_assoc($result);
        
        if ($agent) {
            // Fetch agent reviews
            $reviews = [];
            $review_sql = "SELECT * FROM reviews WHERE agent_email = ?";
            $review_stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($review_stmt, $review_sql)) {
                mysqli_stmt_bind_param($review_stmt, "s", $agent_email);
                mysqli_stmt_execute($review_stmt);
                $review_result = mysqli_stmt_get_result($review_stmt);
                while ($review = mysqli_fetch_assoc($review_result)) {
                    $reviews[] = $review;
                }
            }

            // Fetch agent's properties
            $properties = [];
            $property_sql = "SELECT * FROM property_requests WHERE agent_email = ? AND approved = TRUE";
            $property_stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($property_stmt, $property_sql)) {
                mysqli_stmt_bind_param($property_stmt, "s", $agent_email);
                mysqli_stmt_execute($property_stmt);
                $property_result = mysqli_stmt_get_result($property_stmt);
                while ($property = mysqli_fetch_assoc($property_result)) {
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
                    $properties[] = $property;
                }
            }
        } else {
            echo "Agent not found.";
            exit();
        }
    }
} else {
    echo "Invalid agent email.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Profile - <?php echo htmlspecialchars($agent['full_name']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CasaVista/Style/profile.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <!-- Agent Profile Picture -->
                <div class="profile-picture mb-4">
                    <?php if ($agent['profile_picture']): ?>
                        <img src="<?php echo htmlspecialchars($agent['profile_picture']); ?>" alt="Agent Profile Picture" class="img-fluid rounded-circle">
                    <?php else: ?>
                        <div class="placeholder">No Profile Picture</div>
                    <?php endif; ?>
                </div>
                
                <!-- Agent Information -->
                <h3><?php echo htmlspecialchars($agent['full_name']); ?></h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($agent['email']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($agent['description']); ?></p>
                <p><strong>Total Properties Listed:</strong> <?php echo count($properties); ?></p>
            </div>

            <div class="col-md-8">
                <!-- Reviews Section -->
                <h4>Reviews</h4>
                <?php if (count($reviews) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($reviews as $review): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($review['reviewer_name']); ?>:</strong>
                                <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                                <small class="text-muted"><?php echo htmlspecialchars($review['review_date']); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No reviews available for this agent.</p>
                <?php endif; ?>

                <!-- Leave a Review Form -->
                <form action="leave_review.php" method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="review_text" class="form-label">Leave a Review</label>
                        <textarea name="review_text" id="review_text" class="form-control" rows="3" required></textarea>
                    </div>
                    <input type="hidden" name="agent_email" value="<?php echo htmlspecialchars($agent_email); ?>">
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>

                <!-- Agent Properties Section -->
                <div class="agent-properties mt-5">
                    <h4>Properties Listed by <?php echo htmlspecialchars($agent['full_name']); ?></h4>
                    <div class="row">
                        <?php if (count($properties) > 0): ?>
                            <?php foreach ($properties as $property): ?>
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <?php if (!empty($property['images'][0])): ?>
                                            <img src="<?php echo htmlspecialchars($property['images'][0]); ?>" class="card-img-top" alt="Property Image">
                                        <?php else: ?>
                                            <div class="card-img-top" style="background-color: #e0e0e0; width: 100%; height: 200px; display: flex; align-items: center; justify-content: center;">
                                                <span>No Image Available</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($property['property_name']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($property['property_description']); ?></p>
                                            <p class="card-text">Price: R<?php echo number_format($property['property_price'], 2); ?></p>
                                            <a href="property.php?id=<?php echo $property['id']; ?>" class="btn btn-primary">View Property</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No properties listed by this agent.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

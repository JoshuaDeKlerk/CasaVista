<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

// Fetch the minimum and maximum prices for the slider
$min_price = 0;
$max_price = 0;
$sql = "SELECT MIN(property_price) AS min_price, MAX(property_price) AS max_price FROM property_requests WHERE approved = TRUE";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $min_price = $row['min_price'];
    $max_price = $row['max_price'];
}

// Initialize filter variables
$filters = [
    'price_min' => isset($_GET['price_min']) ? (float)$_GET['price_min'] : $min_price,
    'price_max' => isset($_GET['price_max']) ? (float)$_GET['price_max'] : $max_price,
    'property_type' => isset($_GET['property_type']) ? $_GET['property_type'] : '',
    'bedrooms' => isset($_GET['bedrooms']) ? (int)$_GET['bedrooms'] : 0,
    'bathrooms' => isset($_GET['bathrooms']) ? (int)$_GET['bathrooms'] : 0,
    'property_status' => isset($_GET['property_status']) ? $_GET['property_status'] : ''
];

// Build the SQL query with filters
$sql = "SELECT * FROM property_requests WHERE approved = TRUE";

// Apply filters
if ($filters['price_min'] > 0) {
    $sql .= " AND property_price >= " . $filters['price_min'];
}
if ($filters['price_max'] > 0) {
    $sql .= " AND property_price <= " . $filters['price_max'];
}
if (!empty($filters['property_type'])) {
    $sql .= " AND property_type = '" . mysqli_real_escape_string($conn, $filters['property_type']) . "'";
}
if ($filters['bedrooms'] > 0) {
    $sql .= " AND bedrooms >= " . $filters['bedrooms'];
}
if ($filters['bathrooms'] > 0) {
    $sql .= " AND bathrooms >= " . $filters['bathrooms'];
}
if (!empty($filters['property_status'])) {
    $sql .= " AND property_status = '" . mysqli_real_escape_string($conn, $filters['property_status']) . "'";
}

// Sort by price ascending
$sql .= " ORDER BY property_price ASC";

// Pagination variables
$limit = 28; // Number of properties per page (4 cards per row, 7 rows)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total properties
$total_sql = "SELECT COUNT(*) as total FROM property_requests WHERE approved = TRUE";
$total_result = mysqli_query($conn, $total_sql);
$total_properties = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_properties / $limit);

// Fetch properties with pagination
$sql .= " LIMIT $limit OFFSET $offset";

$properties = [];
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

        $properties[] = $property;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Properties</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CasaVista/Style/browse.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="BrowseContainer">
        <div class="BrowseTitle">
            <h1>Browse Properties</h1>
        </div>
        <form method="GET" action="browse.php">
            <div class="BrowseFilters">
                <div class="FilterTitle">
                    <h2>Filters</h2>
                </div>
                <div class="TopFilters">

                    <div class="PriceFilter formInput">
                        <label for="price_range" class="form-label">Price Range: R<span id="price_min_label"><?php echo number_format($filters['price_min']); ?></span> - R<span id="price_max_label"><?php echo number_format($filters['price_max']); ?></span></label>
                        <input type="range" class="form-range" id="price_range" name="price_range" min="<?php echo $min_price; ?>" max="<?php echo $max_price; ?>" step="1000" value="<?php echo $filters['price_max']; ?>" oninput="updatePriceLabels(this.value)">
                        <input type="hidden" id="price_min" name="price_min" value="<?php echo $filters['price_min']; ?>">
                        <input type="hidden" id="price_max" name="price_max" value="<?php echo $filters['price_max']; ?>">
                    </div>

                    <div class="PropertyTypeFilter formInput">
                        <label for="property_type" class="form-label">Property Type</label>
                        <select name="property_type" id="property_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="Apartment" <?php if ($filters['property_type'] == 'Apartment') echo 'selected'; ?>>Apartment</option>
                            <option value="House" <?php if ($filters['property_type'] == 'House') echo 'selected'; ?>>House</option>
                            <option value="Condo" <?php if ($filters['property_type'] == 'Condo') echo 'selected'; ?>>Condo</option>
                            <option value="Townhouse" <?php if ($filters['property_type'] == 'Townhouse') echo 'selected'; ?>>Townhouse</option>
                            <option value="Villa" <?php if ($filters['property_type'] == 'Villa') echo 'selected'; ?>>Villa</option>
                        </select>
                    </div>

                    <div class="PropertyStatusFilter formInput">
                        <label for="property_status" class="form-label">Property Status</label>
                        <select name="property_status" id="property_status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="For Sale" <?php if ($filters['property_status'] == 'For Sale') echo 'selected'; ?>>For Sale</option>
                            <option value="For Rent" <?php if ($filters['property_status'] == 'For Rent') echo 'selected'; ?>>For Rent</option>
                            <option value="Under Contract" <?php if ($filters['property_status'] == 'Under Contract') echo 'selected'; ?>>Under Contract</option>
                        </select>
                    </div>
                </div>

                <div class="BottomFilters">
                    <div class="BottomFiltersCont">
                        <div class="BathroomFilter formInput">
                            <label for="bathrooms" class="form-label">Bathrooms</label>
                            <input type="number" name="bathrooms" id="bathrooms" class="form-control" value="<?php echo $filters['bathrooms']; ?>" min="0">
                        </div>

                        <div class="BedroomFilter formInput">
                            <label for="bedrooms" class="form-label">Bedrooms</label>
                            <input type="number" name="bedrooms" id="bedrooms" class="form-control" value="<?php echo $filters['bedrooms']; ?>" min="0">
                        </div>
                    </div>

                    <div class="FilterSubmitButton">
                        <button type="submit" class="submitButton">
                            Apply Filters
                        </button>
                    </div>
                </div>

            </div>
        </form>

        <div class="CardContainer">
            <div class="FilterTitle">
                    <h2>Properties</h2>
            </div>
            <div class="row">
                <?php if (count($properties) > 0): ?>
                    <?php foreach ($properties as $property): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="PropertyCard mb-4">
                                <!-- Carousel for property images -->
                                <div id="carousel-<?php echo $property['id']; ?>" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($property['images'] as $index => $image_link): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo htmlspecialchars($image_link); ?>" class="PropertyImage d-block w-100" alt="Property Image">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $property['id']; ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $property['id']; ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>

                                <div class="PropertyDetails card-body">
                                    <h5 class="PropertyTitle card-title"><?php echo htmlspecialchars($property['property_name']); ?></h5>
                                    <p class="PropertyType card-text">
                                        <span>Type: <?php echo htmlspecialchars($property['property_type']); ?></span><br>
                                        <span><?php echo htmlspecialchars($property['property_status']); ?></span>
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
                    <p>No properties available.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for Price Slider -->
    <script>
        function updatePriceLabels(value) {
            document.getElementById('price_max').value = value;
            document.getElementById('price_max_label').innerText = parseFloat(value).toLocaleString();
        }
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('price_range').value = document.getElementById('price_max').value;
            document.getElementById('price_min_label').innerText = parseFloat(document.getElementById('price_min').value).toLocaleString();
            document.getElementById('price_max_label').innerText = parseFloat(document.getElementById('price_max').value).toLocaleString();
        });
    </script>
</body>
</html>

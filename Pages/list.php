<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$email = $_SESSION["user"];

// Check if the user is an agent or admin
$sql = "SELECT user_type FROM user WHERE email = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $user_type = $user['user_type'];

    if ($user_type !== 'agent' && $user_type !== 'admin') {
        echo "You do not have permission to access this page.";
        exit();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs as necessary
    $property_name = mysqli_real_escape_string($conn, $_POST['property_name']);
    $property_status = mysqli_real_escape_string($conn, $_POST['property_status']);
    $property_description = mysqli_real_escape_string($conn, $_POST['property_description']);
    $property_price = mysqli_real_escape_string($conn, $_POST['property_price']);
    $property_type = mysqli_real_escape_string($conn, $_POST['property_type']);
    $total_area = mysqli_real_escape_string($conn, $_POST['total_area']);
    $living_room_size = mysqli_real_escape_string($conn, $_POST['living_room_size']);
    $kitchen_size = mysqli_real_escape_string($conn, $_POST['kitchen_size']);
    $bedrooms = mysqli_real_escape_string($conn, $_POST['bedrooms']);
    $bathrooms = mysqli_real_escape_string($conn, $_POST['bathrooms']);
    $property_condition = mysqli_real_escape_string($conn, $_POST['property_condition']);
    $year_built = mysqli_real_escape_string($conn, $_POST['year_built']);
    $furnishing = mysqli_real_escape_string($conn, $_POST['furnishing']);
    $map_location = mysqli_real_escape_string($conn, $_POST['map_location']);
    $street_city = mysqli_real_escape_string($conn, $_POST['street_city']);
    $state_province = mysqli_real_escape_string($conn, $_POST['state_province']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $available_from = mysqli_real_escape_string($conn, $_POST['available_from']);
    $pet_policy = mysqli_real_escape_string($conn, $_POST['pet_policy']);
    $utilities_included = mysqli_real_escape_string($conn, $_POST['utilities_included']);

    // Insert property data into property_requests table
    $sql = "INSERT INTO property_requests (property_name, property_status, property_description, property_price, property_type, total_area, living_room_size, kitchen_size, bedrooms, bathrooms, property_condition, year_built, furnishing, map_location, street_city, state_province, postal_code, available_from, pet_policy, utilities_included, agent_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssssssssssssssssssss", $property_name, $property_status, $property_description, $property_price, $property_type, $total_area, $living_room_size, $kitchen_size, $bedrooms, $bathrooms, $property_condition, $year_built, $furnishing, $map_location, $street_city, $state_province, $postal_code, $available_from, $pet_policy, $utilities_included, $email);
        mysqli_stmt_execute($stmt);

        // Get the last inserted property ID
        $property_id = mysqli_insert_id($conn);

        // Handle image links (assuming you have input fields for image links)
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($_POST["image_link_$i"])) {
                $image_link = mysqli_real_escape_string($conn, $_POST["image_link_$i"]);
                $image_sql = "INSERT INTO property_images (property_id, image_link) VALUES (?, ?)";
                $image_stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($image_stmt, $image_sql)) {
                    mysqli_stmt_bind_param($image_stmt, "is", $property_id, $image_link);
                    mysqli_stmt_execute($image_stmt);
                }
            }
        }

        // Redirect to a confirmation page or back to the list page
        header("Location: /CasaVista/Pages/list.php?success=1");
        exit();
    } else {
        echo "Something went wrong. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CasaVista/Style/browse.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="BrowseContainer">
        <div class="BrowseTitle">
            <h1>Add Property</h1>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">Property submitted successfully!</div>
        <?php endif; ?>

        <!-- Form for Adding Property -->
        <form action="" method="POST">
            <div class="BrowseFilters">
                <div class="row">
                    <!-- Left Column: Image Links -->
                    <div class="col-md-6">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="mb-3">
                                <label for="image_link_<?php echo $i; ?>" class="form-label">Image Link <?php echo $i; ?></label>
                                <input type="url" name="image_link_<?php echo $i; ?>" id="image_link_<?php echo $i; ?>" class="form-control formInput" placeholder="https://example.com/image<?php echo $i; ?>.jpg">
                            </div>
                        <?php endfor; ?>
                    </div>

                    <!-- Right Column: Property Details -->
                    <div class="col-md-6 d-flex flex-column">
                        <div>
                            <!-- Property Name -->
                            <div class="mb-3">
                                <label for="property_name" class="form-label">Property Name</label>
                                <input type="text" name="property_name" id="property_name" class="form-control formInput" required>
                            </div>

                            <!-- Property Status -->
                            <div class="mb-3">
                                <label for="property_status" class="form-label">Property Status</label>
                                <select name="property_status" id="property_status" class="form-select formInput" required>
                                    <option value="For Sale">For Sale</option>
                                    <option value="For Rent">For Rent</option>
                                    <option value="Under Contract">Under Contract</option>
                                </select>
                            </div>
                        </div>

                        <!-- Spacer to push the description to the bottom -->
                        <div class="flex-grow-1"></div>

                        <!-- Property Description -->
                        <div class="mb-3">
                            <label for="property_description" class="form-label">Property Description</label>
                            <textarea name="property_description" id="property_description" class="form-control formInput" rows="5" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Additional Fields -->
                <div class="row mt-4">
                    <!-- Property Price -->
                    <div class="col-md-6 mb-3">
                        <label for="property_price" class="form-label">Price</label>
                        <input type="text" name="property_price" id="property_price" class="form-control formInput" required>
                    </div>

                    <!-- Property Type -->
                    <div class="col-md-6 mb-3">
                        <label for="property_type" class="form-label">Property Type</label>
                        <select name="property_type" id="property_type" class="form-select formInput" required>
                            <option value="Apartment">Apartment</option>
                            <option value="House">House</option>
                            <option value="Condo">Condo</option>
                            <option value="Townhouse">Townhouse</option>
                            <option value="Villa">Villa</option>
                        </select>
                    </div>

                    <!-- Total Area -->
                    <div class="col-md-4 mb-3">
                        <label for="total_area" class="form-label">Total Area (sq ft)</label>
                        <input type="number" name="total_area" id="total_area" class="form-control formInput" required>
                    </div>

                    <!-- Living Room Size -->
                    <div class="col-md-4 mb-3">
                        <label for="living_room_size" class="form-label">Living Room Size (sq ft)</label>
                        <input type="number" name="living_room_size" id="living_room_size" class="form-control formInput" required>
                    </div>

                    <!-- Kitchen Size -->
                    <div class="col-md-4 mb-3">
                        <label for="kitchen_size" class="form-label">Kitchen Size (sq ft)</label>
                        <input type="number" name="kitchen_size" id="kitchen_size" class="form-control formInput" required>
                    </div>

                    <!-- Bedrooms -->
                    <div class="col-md-4 mb-3">
                        <label for="bedrooms" class="form-label">Total Bedrooms</label>
                        <input type="number" name="bedrooms" id="bedrooms" class="form-control formInput" required>
                    </div>

                    <!-- Bathrooms -->
                    <div class="col-md-4 mb-3">
                        <label for="bathrooms" class="form-label">Total Bathrooms</label>
                        <input type="number" name="bathrooms" id="bathrooms" class="form-control formInput" required>
                    </div>

                    <!-- Property Condition -->
                    <div class="col-md-4 mb-3">
                        <label for="property_condition" class="form-label">Property Condition</label>
                        <select name="property_condition" id="property_condition" class="form-select formInput" required>
                            <option value="New">New</option>
                            <option value="Recently Renovated">Recently Renovated</option>
                            <option value="Needs Renovation">Needs Renovation</option>
                        </select>
                    </div>

                    <!-- Year Built -->
                    <div class="col-md-4 mb-3">
                        <label for="year_built" class="form-label">Year Built</label>
                        <input type="number" name="year_built" id="year_built" class="form-control formInput" required>
                    </div>

                    <!-- Furnishing -->
                    <div class="col-md-4 mb-3">
                        <label for="furnishing" class="form-label">Furnishing</label>
                        <select name="furnishing" id="furnishing" class="form-select formInput" required>
                            <option value="Furnished">Furnished</option>
                            <option value="Semi-Furnished">Semi-Furnished</option>
                            <option value="Unfurnished">Unfurnished</option>
                        </select>
                    </div>

                    <!-- Map of Location -->
                    <div class="col-md-12 mb-3">
                        <label for="map_location" class="form-label">Map of Location</label>
                        <input type="text" name="map_location" id="map_location" class="form-control formInput" placeholder="https://maps.google.com/...">
                    </div>

                    <!-- Address Information -->
                    <div class="col-md-4 mb-3">
                        <label for="street_city" class="form-label">Street and City</label>
                        <input type="text" name="street_city" id="street_city" class="form-control formInput" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="state_province" class="form-label">State/Province</label>
                        <input type="text" name="state_province" id="state_province" class="form-control formInput" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="postal_code" class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" class="form-control formInput" required>
                    </div>

                    <!-- Available From -->
                    <div class="col-md-4 mb-3">
                        <label for="available_from" class="form-label">Available From</label>
                        <input type="date" name="available_from" id="available_from" class="form-control formInput" required>
                    </div>

                    <!-- Pet Policy -->
                    <div class="col-md-4 mb-3">
                        <label for="pet_policy" class="form-label">Pet Policy</label>
                        <select name="pet_policy" id="pet_policy" class="form-select formInput" required>
                            <option value="Pets Allowed">Pets Allowed</option>
                            <option value="No Pets Allowed">No Pets Allowed</option>
                        </select>
                    </div>

                    <!-- Utilities Included -->
                    <div class="col-md-4 mb-3">
                        <label for="utilities_included" class="form-label">Utilities Included</label>
                        <textarea name="utilities_included" id="utilities_included" class="form-control formInput" rows="3" required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-12">
                        <div class="FilterSubmitButton">
                            <button type="submit" class="submitButton">Submit Property</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    // Move the property from property_requests to properties table
    $sql = "INSERT INTO properties (property_name, property_status, property_description, property_price, property_type, total_area, living_room_size, kitchen_size, bedrooms, bathrooms, property_condition, year_built, furnishing, map_location, street_city, state_province, postal_code, available_from, pet_policy, utilities_included, agent_email)
            SELECT property_name, property_status, property_description, property_price, property_type, total_area, living_room_size, kitchen_size, bedrooms, bathrooms, property_condition, year_built, furnishing, map_location, street_city, state_province, postal_code, available_from, pet_policy, utilities_included, agent_email
            FROM property_requests WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $property_id);
        mysqli_stmt_execute($stmt);

        // After approval, delete the request
        $delete_sql = "DELETE FROM property_requests WHERE id = ?";
        $delete_stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($delete_stmt, $delete_sql)) {
            mysqli_stmt_bind_param($delete_stmt, "i", $property_id);
            mysqli_stmt_execute($delete_stmt);
        }
    }
}

header("Location: /CasaVista/Pages/admin.php");
exit();
?>

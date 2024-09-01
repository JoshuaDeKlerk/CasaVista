<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    $sql = "DELETE FROM property_requests WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $property_id);
        mysqli_stmt_execute($stmt);
    }
}

header("Location: /CasaVista/Pages/admin.php");
exit();
?>

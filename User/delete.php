<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

    if (!isset($_SESSION["user"])) {
        header("Location: ./logIn.php");
        exit();
    }

    $email = $_SESSION["user"];

    // SQL to delete the account
    $sql = "DELETE FROM user WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        
        // Destroy the session after deleting the account
        session_destroy();

        // Redirect to the login page or a goodbye page
        header("Location: ./logIn.php");
        exit();
    } else {
        echo "Something went wrong. Please try again later.";
    }
?>

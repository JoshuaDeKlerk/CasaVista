<?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: /CasaVista/User/logIn.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="/CasaVista/Style/home.css"> <!-- Link to the home CSS -->
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-overlay">
            <h1 class="hero-title">Real Estate 
                <br>Around 
                <br>The World</h1>
            <a href="/CasaVista/Pages/browse.php" class="hero-button">PROPERTIES</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

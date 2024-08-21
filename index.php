<?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: User/logIn.php");
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
    <link rel="stylesheet" href="/Users/signUpInStyle.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to CasaVista</h1>
        <a href="./User/logOut.php" class="btn btn-warning">Logout</a>
    </div>
</body>
</html>
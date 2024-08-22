<?php
    $hostname = "localhost";
    $dbUser = "root";  // Make sure this matches your server's database username
    $dbPassword = "";  // Make sure this matches your server's database password
    $dbName = "casavista";
    
    // Create connection
    $conn = mysqli_connect($hostname, $dbUser, $dbPassword, $dbName);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>
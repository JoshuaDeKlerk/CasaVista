<?php
    $hostname = "localhost";
    $dbUSer = "root";
    $dbPassword = "";
    $dbName = "casavista";
    
    $conn = mysqli_connect($hostname, $dbUSer, $dbPassword, $dbName);
        if (!$conn) {
            die("Something went wrong!");
        }

?>
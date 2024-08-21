<?php
    session_start();
    if (isset($_SESSION["user"])) {
        header("Location: ../index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="userStyle.css">

</head>
<body>

    <div class="LogInFormContainer">
        <?php 
            if (isset($_POST["Login"])) {
                $email = $_POST["email"];
                $password = $_POST["password"];
                    require_once "../server.php";
                    $sql = "SELECT * FROM user WHERE email = '$email'";
                    $result = mysqli_query($conn, $sql);
                    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    if ($user) {
                        if (password_verify($password, $user["password"])) {
                            
                            session_start();
                            $_SESSION["user"] = "yes";
                            
                            header("Location: ../index.php");
                            die();
                        }else {
                            echo "<div class='alert alert-danger'>Password incorrect</div>";
                        }
                    }else {
                        echo "<div class='alert alert-danger'>Email does not exist</div>";
                    }
            }
        ?>
        <form action="LogIn.php" method="post">

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Login" name="Login">
            </div>

        </form>
        <div><p>Not registered yet <a href="./signUp.php">Register Here</a></p></div>
    </div>

</body>
</html>
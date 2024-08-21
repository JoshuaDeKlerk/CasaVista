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
    <title>Register</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="userStyle.css">

</head>
<body>

    <div class="SignUpFormContainer">
        <?php
            if (isset($_POST["submit"])) {
                $fullName = $_POST["fullName"];
                $email = $_POST["email"];
                $userType = $_POST["userType"];
                $dateOfBirth = $_POST["dob"];
                $password = $_POST["password"];
                $passwordRepeat = $_POST["confirmPassword"];

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $errors = array();

                if (empty($fullName) || empty($email) || empty($userType) || empty($dateOfBirth) || empty($password) || empty($passwordRepeat)) {
                    array_push($errors, "All fields are required");
                }
                
                if (empty($userType)) {
                    array_push($errors, "User type is required");
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errors, "Email is not valid");
                }

                if (strlen($password) < 6) {
                    array_push($errors, "Password too short");
                }

                if ($password !== $passwordRepeat) {
                    array_push($errors, "Password does not match");
                }

                require_once "../server.php";
                $sql = "SELECT * FROM user WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);
                $rowCount = mysqli_num_rows($result);
                if ($rowCount>0) {
                    array_push($errors, "Email already exists!");
                }

                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                } else {
    
                    $sql = "INSERT INTO user (full_name, email, user_type, date_of_birth, password) VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                    if ($prepareStmt) {
                        mysqli_stmt_bind_param($stmt, "sssss", $fullName, $email, $userType, $dateOfBirth, $passwordHash);
                        mysqli_stmt_execute($stmt);
                        echo "<div class='alert alert-success'>You are registered successfully.</div>";
                    }else {
                        die("Something went wrong!");
                    }
                }
            }
        ?>

        <form action="signUp.php" method="post">
            
            <div class="mb-3">
                <label for="fullName" class="form-label">Name and Surname</label>
                <input type="text" class="form-control" id="fullName" name="fullName">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>

            <!-- Button group to select role -->
            <div class="mb-3">
                Choose your role
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" id="User" onclick="selectOption('User')">User</button>
                    <button type="button" class="btn btn-outline-primary" id="Agent" onclick="selectOption('Agent')">Agent</button>
                </div>
            </div>
            <input type="hidden" id="userType" name="userType">

            <!-- Date of Birth Picker -->
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>

            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div><p>Already Registered <a href="./logIn.php">Login Here</a></p></div>
    </div>

    <script>
        function selectOption(option) {
            // Remove active class from all buttons
            document.querySelectorAll('.btn-group .btn').forEach(function(button) {
                button.classList.remove('active');
            });

            // Add active class to the selected button
            document.getElementById(option).classList.add('active');

            // Set the value of the hidden input
            document.getElementById('userType').value = option;
        }

        // Set the max date for the date picker to 18 years ago from today
        document.addEventListener('DOMContentLoaded', function() {
            const dobInput = document.getElementById('dob');
            const today = new Date();
            const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
            dobInput.max = maxDate.toISOString().split('T')[0];
        });
    </script>
</body>
</html>

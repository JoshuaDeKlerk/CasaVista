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
        <div class="logInLeft">
            <div class="loginLeftCont">
                <?php 
                    if (isset($_POST["Login"])) {
                        $email = $_POST["email"];
                        $password = $_POST["password"];
                        require_once "../server.php";
                        $sql = "SELECT * FROM user WHERE email = '$email'";
                        $result = mysqli_query($conn, $sql);
                        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                        $errors = array();

                        if ($user) {
                            if (password_verify($password, $user["password"])) {
                                session_start();
                                $_SESSION["user"] = "yes";
                                header("Location: ../index.php");
                                die();
                            } else {
                                $errors[] = "Password incorrect";
                            }
                        } else {
                            $errors[] = "Email does not exist";
                        }

                        if (!empty($errors)) {
                            echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var toastContainer = document.querySelector('.toast-container');
                            ";
                            foreach ($errors as $error) {
                                $toastHtml = "<div class='toast align-items-center border-0' aria-live='assertive' aria-atomic='true'>
                                                <div class='d-flex'>
                                                    <div class='toast-body'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</div>
                                                    <button type='button' class='btn-close me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
                                                </div>
                                            </div>";
                                echo "toastContainer.insertAdjacentHTML('beforeend', `" . addslashes($toastHtml) . "`);";
                            }
                            echo "
                                        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
                                        var toastList = toastElList.map(function (toastEl) {
                                            return new bootstrap.Toast(toastEl, { autohide: false });
                                        });
                                        toastList.forEach(toast => toast.show());
                                    });
                                </script>";
                        }
                    }
                ?>
                
                <!-- Toast Container -->
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <!-- Toasts will be injected here -->
                </div>

                <div class="FormCont">
                    <form action="LogIn.php" method="post">
                        
                        <div class="formTitle">
                            <h1>Login to your account</h1>
                            <h2>Explore the world of Retail</h2>
                        </div>

                        <div class="formInformation">
                            
                            <div class="emailFormCont">
                                <div class="formInput">
                                    <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>">
                                    <img src="../Assets/User/Email.svg" class="icon" alt="icon">
                                </div>
                                <div class="formText">We'll never share your email with anyone else.</div>
                            </div> 

                            <div class="formInput">
                                <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                                <img src="../Assets/User/Password.svg" class="icon" alt="icon">
                            </div>

                            <div class="registerButtonCont" style="margin-top: 20px;">
                                <div class="form-btn">
                                    <input type="submit" class="btn btn-primary" value="Login" name="Login">
                                </div>

                                <div class="loginLinkText"><p>Not registered yet <a href="./signUp.php">Register Here</a></p></div>
                            </div>
                        </div>
                    </form>
                </div>
        
                
            </div>
            
        </div>
        <div class="logInRight">
        </div>
    </div>
        

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

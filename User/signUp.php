<?php
    session_start();
    if (isset($_SESSION["user"])) {
        header("Location: ../index.php");
        exit();
    }

    // Initialize variables to hold form values
    $fullName = $email = $userType = $dateOfBirth = $password = $passwordRepeat = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    
    <link rel="stylesheet" href="./userStyle.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    
    <div class="SignUpFormContainer">
        <div class="SignUpLeft">
            <!-- Left Side Content -->
        </div>
        <div class="SignUpRight">
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

                    // Check if any field is empty
                    if (empty($fullName) || empty($email) || empty($userType) || empty($dateOfBirth) || empty($password) || empty($passwordRepeat)) {
                        array_push($errors, "All fields are required");
                    } else {
                        // Perform other validations only if all fields are filled
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            array_push($errors, "Email is not valid");
                        }

                        if (strlen($password) < 6) {
                            array_push($errors, "Password too short");
                        }

                        if ($password !== $passwordRepeat) {
                            array_push($errors, "Password does not match");
                        }

                        // Validate date of birth for age 18+
                        $dob = DateTime::createFromFormat('d/m/Y', $dateOfBirth);
                        if (!$dob || $dob->format('d/m/Y') !== $dateOfBirth) {
                            array_push($errors, "Invalid date format. Please use dd/mm/yyyy.");
                        } else {
                            $today = new DateTime();
                            $age = $today->diff($dob)->y;
                            if ($age < 18) {
                                array_push($errors, "You must be 18 years or older.");
                            }

                            // Convert the date to MySQL format (YYYY-MM-DD)
                            $dateOfBirth = $dob->format('Y-m-d');
                        }

                        require_once "../server.php";
                        $sql = "SELECT * FROM user WHERE email = '$email'";
                        $result = mysqli_query($conn, $sql);
                        $rowCount = mysqli_num_rows($result);
                        if ($rowCount > 0) {
                            array_push($errors, "Email already exists!");
                        }
                    }

                    if (count($errors) > 0) {
                        echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var toastContainer = document.querySelector('.toast-container');
                        ";
                        foreach ($errors as $error) {
                            $toastHtml = "<div class='toast align-items-center text-bg-danger border-0' role='alert' aria-live='assertive' aria-atomic='true'>
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
                    } else {
                        $sql = "INSERT INTO user (full_name, email, user_type, date_of_birth, password) VALUES (?, ?, ?, ?, ?)";
                        $stmt = mysqli_stmt_init($conn);
                        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                        if ($prepareStmt) {
                            mysqli_stmt_bind_param($stmt, "sssss", $fullName, $email, $userType, $dateOfBirth, $passwordHash);
                            mysqli_stmt_execute($stmt);

                            // Automatically log the user in and redirect to index.php
                            $_SESSION["user"] = $email;
                            header("Location: ../index.php");
                            exit();
                        } else {
                            die("Something went wrong!");
                        }
                    }
                }
            ?>
            <!-- Toast Container -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <!-- Toasts will be injected here -->
            </div> 

            <div class="FormCont">
                <form action="signUp.php" method="post" onsubmit="return validateDOB()">
                    <div class="formTitle">
                        <h1>Create an account</h1>
                    </div>

                    <div class="formInformation">
                        <div class="formInput">
                            <input type="text" class="form-control" placeholder="Name and Surname" id="fullName" name="fullName" value="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="../Assets/User/Profile.svg" class="icon" alt="icon">
                        </div>

                        <div class="emailFormCont">
                            <div class="formInput">
                                <input type="email" class="form-control" placeholder="Email" id="email" name="email" aria-describedby="emailHelp" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                                <img src="../Assets/User/Email.svg" class="icon" alt="icon">
                            </div>
                            <div class="formText">We'll never share your email with anyone else.</div>
                        </div>                    

                        <!-- Div group to select role -->
                        <div class="formInput role-selection">
                            <p>Choose your role</p>
                            <div class="btn-group">
                                <div class="btn-custom <?php echo ($userType == 'User') ? 'active' : ''; ?>" id="User" onclick="selectOption('User')">
                                    User
                                    <img src="../Assets/User/User.svg" height="28" class="bi bi-search" alt="User Icon">
                                </div>
                                <div class="btn-custom <?php echo ($userType == 'Agent') ? 'active' : ''; ?>" id="Agent" onclick="selectOption('Agent')">
                                    Agent
                                    <img src="../Assets/User/Agent.svg" height="28" class="bi bi-1-circle" alt="Agent Icon">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="userType" name="userType" value="<?php echo htmlspecialchars($userType, ENT_QUOTES, 'UTF-8'); ?>">

                        <!-- Date of Birth Picker -->
                        <div class="formInput birthCont">
                            <p>Birth Date</p>
                            <div class="date-input-wrapper">
                                <input type="text" class="form-control" id="dob" name="dob" placeholder="dd/mm/yyyy" maxlength="10">
                                <img src="../Assets/User/Date.svg" class="date-icon" alt="Custom Calendar Icon">
                            </div>
                        </div>

                        <div class="formInput">
                            <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                            <img src="../Assets/User/Password.svg" class="icon" alt="icon">
                        </div>

                        <div class="formInput">
                            <input type="password" class="form-control" placeholder="Confirm Password" id="confirmPassword" name="confirmPassword">
                            <img src="../Assets/User/Password.svg" class="icon" alt="icon">
                        </div>
                        
                        <div class="registerButtonCont">
                            <div class="form-btn">
                                <input type="submit" class="btn" value="Register" name="submit">
                            </div>

                            <div class="loginLinkText"><p>Already have an account <a href="./logIn.php">Login Here</a></p></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        function selectOption(option) {
            // Remove active class from all role divs
            document.querySelectorAll('.btn-group .btn-custom').forEach(function(button) {
                button.classList.remove('active');
            });

            // Add active class to the selected role div
            document.getElementById(option).classList.add('active');

            // Set the value of the hidden input to the selected option
            document.getElementById('userType').value = option;
        }

        // Function to format input as dd/mm/yyyy
        function formatDateInput(input) {
            const inputValue = input.value.replace(/\D/g, ""); // Remove non-numeric characters
            let formattedValue = "";

            if (inputValue.length <= 2) {
                formattedValue = inputValue;
            } else if (inputValue.length <= 4) {
                formattedValue = inputValue.slice(0, 2) + "/" + inputValue.slice(2);
            } else {
                formattedValue = inputValue.slice(0, 2) + "/" + inputValue.slice(2, 4) + "/" + inputValue.slice(4, 8);
            }

            input.value = formattedValue;
        }

        document.addEventListener('DOMContentLoaded', function() {
        // Calculate the default date (18 years ago from today)
        const defaultDate = new Date();
        defaultDate.setFullYear(defaultDate.getFullYear() - 18);

        // Initialize Flatpickr on the date input
        flatpickr("#dob", {
            dateFormat: "d/m/Y",  // Format the date as dd/mm/yyyy
            allowInput: true,  // Allow manual typing
            clickOpens: false,  // Prevent automatic opening on input click
            onOpen: function(selectedDates, dateStr, instance) {
                // Open the calendar on a date 18 years ago
                instance.setDate(defaultDate, false);
            }
        });

        // Trigger the date picker when the icon is clicked
        document.querySelector('.date-icon').addEventListener('click', function() {
            document.querySelector("#dob")._flatpickr.open();  // Open Flatpickr
        });

        // Add event listener to format input as the user types
        const dobInput = document.getElementById('dob');
        dobInput.addEventListener('input', function() {
            formatDateInput(dobInput);
        });
    });

    </script>
</body>
</html>

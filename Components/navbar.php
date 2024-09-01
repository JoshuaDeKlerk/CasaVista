<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

    $user_type = 'user'; // Default to 'user'
    $full_name = 'Profile'; // Default to 'Profile'
    $profile_picture = ''; // Default profile picture

    if (isset($_SESSION["user"])) {
        $email = $_SESSION["user"];
        
        // Fetch the user's info from the database
        $sql = "SELECT full_name, user_type, profile_picture FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $user = mysqli_fetch_assoc($result);
            if ($user) {
                $user_type = $user['user_type'];
                $full_name = $user['full_name'];
                $profile_picture = $user['profile_picture'];
            } else {
                error_log("User not found with email: $email");
            }
        } else {
            error_log("Query failed: " . mysqli_error($conn));
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CasaVista Navbar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CasaVista/Style/navbar.css">
</head>
<body>
<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="/CasaVista/index.php">
            <img src="/CasaVista/Assets/Nav/Logo.svg" alt="Logo" height="30" class="navbar-logo">
        </a>

        <!-- Navbar Toggle for Mobile View -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
            <!-- Centered Navbar Links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link icon" href="/CasaVista/index.php">
                         Home 
                         <img src="/CasaVista/Assets/Nav/Home.svg" alt="Home Icon">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link icon" href="/CasaVista/Pages/browse.php">
                        Browse
                        <img src="/CasaVista/Assets/Nav/Browse.svg" alt="Browse Icon">
                    </a>
                </li>
                <?php if ($user_type !== 'user'): ?>
                    <li class="nav-item">
                        <a class="nav-link icon" href="/CasaVista/Pages/list.php">
                           List
                           <img src="/CasaVista/Assets/Nav/List.svg" alt="List Icon">
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link icon" href="/CasaVista/Pages/favourites.php">
                        Favourites
                        <img src="/CasaVista/Assets/Nav/Favourites.svg" alt="Favourites Icon">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link icon" href="/CasaVista/Pages/cart.php">
                        Cart
                        <img src="/CasaVista/Assets/Nav/Cart.svg" alt="Cart Icon">
                    </a>
                </li>
                <?php if ($user_type === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link icon" href="/CasaVista/Pages/admin.php">
                            Admin
                            <img src="/CasaVista/Assets/Nav/Admin.svg" alt="Admin Icon">
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Profile Dropdown on the Right -->
        <div class="d-flex ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Profile Picture -->
                        <div class="profile-picture-placeholder <?php echo $profile_picture ? 'has-image' : ''; ?>">
                            <?php if ($profile_picture): ?>
                                <img src="<?php echo htmlspecialchars('/CasaVista' . $profile_picture); ?>" alt="Profile Picture" class="profile-picture">
                            <?php else: ?>
                                <img src="/CasaVista/Assets/Nav/Profile.svg" alt="Profile Picture Placeholder" class="profile-picture">
                            <?php endif; ?>
                        </div>
                        <!-- User's Full Name -->
                        <span class="profile-text"><?php echo htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/CasaVista/Pages/settings.php">Settings</a></li>
                        <?php if ($user_type === 'agent' || $user_type === 'admin'): ?>
                            <li><a class="dropdown-item" href="/CasaVista/Pages/Properties.php">Properties</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="/CasaVista/User/logOut.php">Logout</a></li>
                        <li><a class="dropdown-item" href="#" onclick="confirmDeletion()">Delete Account</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    function confirmDeletion() {
        if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
            window.location.href = "/CasaVista/User/delete.php";
        }
    }
</script>
</body>
</html>

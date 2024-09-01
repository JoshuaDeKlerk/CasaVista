<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$email = $_SESSION["user"];
$user_type = '';
$full_name = '';
$description = '';
$profile_picture = '';
$date_of_birth = '';

// Fetch the user's current data
$sql = "SELECT * FROM user WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $user = mysqli_fetch_assoc($result);
    $full_name = $user['full_name'];
    $user_type = $user['user_type'];
    $profile_picture = $user['profile_picture'];
    $description = $user_type === 'agent' ? $user['description'] : '';
    $date_of_birth = $user['date_of_birth'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $description = $user_type === 'agent' ? $_POST['description'] : '';

    // Check if a new profile picture is uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Directory where the file should be uploaded
        $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/uploads/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Delete the old profile picture if it exists and isn't a default image
            if ($profile_picture && file_exists($_SERVER['DOCUMENT_ROOT'] . $profile_picture)) {
                if (!unlink($_SERVER['DOCUMENT_ROOT'] . $profile_picture)) {
                    error_log("Failed to delete old profile picture: " . $_SERVER['DOCUMENT_ROOT'] . $profile_picture);
                } else {
                    error_log("Old profile picture deleted: " . $_SERVER['DOCUMENT_ROOT'] . $profile_picture);
                }
            } else {
                error_log("Old profile picture does not exist or cannot be found: " . $_SERVER['DOCUMENT_ROOT'] . $profile_picture);
            }

            // Update profile picture path in the database
            $profile_picture = '/uploads/' . $newFileName;

            $sql = "UPDATE user SET profile_picture = ? WHERE email = ?";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $profile_picture, $email);
                mysqli_stmt_execute($stmt);
            }
        } else {
            echo "Error uploading the profile picture.";
            error_log("Error moving uploaded file to destination path: " . $dest_path);
        }
    }

    // Update other user information in the database
    $sql = "UPDATE user SET full_name = ?, date_of_birth = ?, description = ? WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $date_of_birth, $description, $email);
        mysqli_stmt_execute($stmt);
        header("Location: settings.php");
        exit();
    } else {
        echo "Error updating the profile.";
        error_log("Error updating user information in the database.");
    }
}

// Handle review deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_review'])) {
    $review_id = $_POST['review_id'];
    $delete_sql = "DELETE FROM reviews WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $delete_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $review_id);
        mysqli_stmt_execute($stmt);
        header("Location: settings.php");
        exit();
    } else {
        echo "Something went wrong. Please try again later.";
    }
}

// Fetch agent/admin reviews if applicable
$reviews = [];
if ($user_type === 'agent' || $user_type === 'admin') {
    $review_sql = "SELECT * FROM reviews WHERE agent_email = '$email'";
    $review_result = mysqli_query($conn, $review_sql);
    if ($review_result) {
        while ($review = mysqli_fetch_assoc($review_result)) {
            $reviews[] = $review;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        /* Custom styles for circular profile picture */
        .profile-picture {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            background-color: black;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        .profile-picture.has-image img {
            display: block;
        }
        .profile-picture.has-image {
            background-color: transparent;
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <!-- Side Navigation -->
            <div class="col-md-3">
                <div class="list-group">
                    <a href="#" id="account-link" class="list-group-item list-group-item-action active" onclick="showSection('account-settings', 'account-link')">Account Settings</a>
                    <?php if ($user_type === 'agent' || $user_type === 'admin'): ?>
                        <a href="#" id="reviews-link" class="list-group-item list-group-item-action" onclick="showSection('reviews', 'reviews-link')">Reviews</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <!-- Account Settings Section -->
                <div id="account-settings">
                    <h2>Account Settings</h2>
                    <form action="/CasaVista/Pages/settings.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label><br>
                            <div id="profilePictureContainer" class="profile-picture <?php echo $profile_picture ? 'has-image' : ''; ?>">
                                <?php if ($profile_picture): ?>
                                    <img src="<?php echo htmlspecialchars('/CasaVista' . $profile_picture); ?>" alt="Profile Picture" id="profilePicturePreview">
                                <?php else: ?>
                                    <img src="" alt="Profile Picture" id="profilePicturePreview">
                                <?php endif; ?>
                            </div>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control" onchange="previewImage(event)">
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">User Type</label>
                            <select name="user_type" id="user_type" class="form-control" <?php echo $user_type === 'admin' ? 'disabled' : ''; ?>>
                                <option value="user" <?php echo $user_type === 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="agent" <?php echo $user_type === 'agent' ? 'selected' : ''; ?>>Agent</option>
                                <?php if ($user_type === 'admin'): ?>
                                    <option value="admin" selected>Admin</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($date_of_birth); ?>" required>
                        </div>
                        <?php if ($user_type === 'agent'): ?>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>

                <!-- Reviews Section -->
                <?php if ($user_type === 'agent' || $user_type === 'admin'): ?>
                    <div id="reviews" class="mt-5" style="display: none;">
                        <h2>Reviews</h2>
                        <?php if (count($reviews) > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($reviews as $review): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($review['reviewer_name']); ?>:</strong>
                                            <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                                            <small class="text-muted"><?php echo htmlspecialchars($review['review_date']); ?></small>
                                        </div>
                                        <!-- Delete Button -->
                                        <form method="POST" action="/CasaVista/Pages/settings.php" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <button type="submit" name="delete_review" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No reviews available.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to Preview Image and Toggle Sections -->
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('profilePicturePreview');
                output.src = reader.result;
                document.getElementById('profilePictureContainer').classList.add('has-image');
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function showSection(sectionId, linkId) {
            // Hide all sections initially
            var sections = ['account-settings', 'reviews'];
            sections.forEach(function(id) {
                var section = document.getElementById(id);
                if (section) {
                    section.style.display = 'none';
                }
            });

            // Show the selected section
            var selectedSection = document.getElementById(sectionId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }

            // Remove active class from all links
            var links = ['account-link', 'reviews-link'];
            links.forEach(function(id) {
                var link = document.getElementById(id);
                if (link) {
                    link.classList.remove('active');
                }
            });

            // Add active class to the selected link
            var selectedLink = document.getElementById(linkId);
            if (selectedLink) {
                selectedLink.classList.add('active');
            }
        }

        // By default, show the account settings section
        document.addEventListener('DOMContentLoaded', function() {
            showSection('account-settings', 'account-link');
        });
    </script>
</body>
</html>

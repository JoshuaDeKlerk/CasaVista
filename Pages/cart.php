<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$email = $_SESSION["user"];

// Fetch cart items with images
$cart_items = [];
$sql = "SELECT pr.*, cart.id AS cart_id, 
        (SELECT image_link FROM property_images WHERE property_id = pr.id LIMIT 1) AS image_link
        FROM property_requests pr
        JOIN cart ON pr.id = cart.property_id 
        WHERE cart.user_email = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($item = mysqli_fetch_assoc($result)) {
        $cart_items[] = $item;
    }
}

// Handle removing items from the cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];
    $remove_sql = "DELETE FROM cart WHERE id = ?";
    $remove_stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($remove_stmt, $remove_sql)) {
        mysqli_stmt_bind_param($remove_stmt, "i", $cart_id);
        mysqli_stmt_execute($remove_stmt);
        // Refresh the page to show the updated cart
        header("Location: cart.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CasaVista/Style/cart.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="CartContainer">
        <h2>Your Cart</h2>
        <?php if (count($cart_items) > 0): ?>
            <ul class="list-group">
                <?php foreach ($cart_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="property-info d-flex align-items-center">
                            <img src="<?php echo htmlspecialchars($item['image_link'] ?? '/path/to/default/image.jpg'); ?>" alt="Property Image" class="property-image">
                            <div class="ms-3">
                                <h5><?php echo htmlspecialchars($item['property_name']); ?></h5>
                                <p>Price: R<?php echo number_format($item['property_price'], 2); ?></p>
                            </div>
                        </div>
                        <div class="btn-group">
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form action="checkout.php" method="POST" class="mt-4">
                <button type="submit" class="btn btn-success btn-lg">Checkout</button>
            </form>
        <?php else: ?>
            <p class="text-center">Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/CasaVista/server.php";

if (!isset($_SESSION["user"])) {
    header("Location: /CasaVista/User/logIn.php");
    exit();
}

$email = $_SESSION["user"];

// Fetch cart items for checkout
$cart_items = [];
$sql = "SELECT property_requests.*, cart.id AS cart_id FROM property_requests 
        JOIN cart ON property_requests.id = cart.property_id 
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

// Handle checkout
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Here you would typically handle payment processing
    // For the sake of this example, we'll assume the payment is successful

    // Remove items from the cart
    $remove_sql = "DELETE FROM cart WHERE user_email = ?";
    $remove_stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($remove_stmt, $remove_sql)) {
        mysqli_stmt_bind_param($remove_stmt, "s", $email);
        mysqli_stmt_execute($remove_stmt);
    }

    // Redirect to a confirmation page or back to the cart with a success message
    header("Location: /CasaVista/Pages/checkout_success.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/CasaVista/Components/navbar.php'; ?>

    <div class="container mt-5">
        <h2>Checkout</h2>
        <p>Review your order below and confirm your purchase.</p>
        <?php if (count($cart_items) > 0): ?>
            <ul class="list-group mb-4">
                <?php foreach ($cart_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5><?php echo htmlspecialchars($item['property_name']); ?></h5>
                            <p>Price: R<?php echo number_format($item['property_price'], 2); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form action="checkout.php" method="POST">
                <button type="submit" class="btn btn-success">Confirm Purchase</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

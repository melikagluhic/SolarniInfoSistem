<?php
// Start session
session_start();
require 'db/db_connection.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the cart is not empty
if (empty($_SESSION['cart'])) {
    header("Location: shopping_cart.php");
    exit;
}

// Process orders
$user_id = $_SESSION['user_id'];
$order_success = true;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    // Insert each item into the orders table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);

    if (!$stmt->execute()) {
        $order_success = false;
        break;
    }
}

// Clear the cart after processing
if ($order_success) {
    $_SESSION['cart'] = [];
    header("Location: user_profile.php?success=Narudžba uspješno izvršena.");
    exit;
} else {
    header("Location: shopping_cart.php?error=Došlo je do greške pri narudžbi.");
    exit;
}
?>

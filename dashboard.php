<?php
// Start session
session_start();
require 'db/db_connection.php'; // Include database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch counts for dashboard statistics
$userCountQuery = "SELECT COUNT(*) AS count FROM users";
$productCountQuery = "SELECT COUNT(*) AS count FROM products";
$orderCountQuery = "SELECT COUNT(*) AS count FROM orders";

$userCount = $conn->query($userCountQuery)->fetch_assoc()['count'];
$productCount = $conn->query($productCountQuery)->fetch_assoc()['count'];
$orderCount = $conn->query($orderCountQuery)->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Administrator Dashboard</h1>
    <div class="stats">
        <div class="stat">
            <h3>Broj Korisnika</h3>
            <p><?= $userCount ?></p>
        </div>
        <div class="stat">
            <h3>Broj Proizvoda</h3>
            <p><?= $productCount ?></p>
        </div>
        <div class="stat">
            <h3>Broj Narudžbi</h3>
            <p><?= $orderCount ?></p>
        </div>
    </div>

    <!-- Management Links -->
    <div class="actions" style="text-align: center; margin-top: 20px;">
        <h2>Upravljanje</h2>
        <a href="admin_users.php">Upravljanje Korisnicima</a> |
        <a href="admin_products.php">Upravljanje Proizvodima</a> |
        <a href="orders.php">Pregled Narudžbi</a>
    </div>

    <a href="logout.php" style="display: block; text-align: center; margin-top: 20px;">Logout</a>
</body>
</html>

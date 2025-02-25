<?php
// Start session
session_start();
require 'db/db_connection.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch user's orders
$order_query = "
    SELECT orders.id, products.name AS product_name, orders.quantity, orders.created_at, orders.status 
    FROM orders 
    JOIN products ON orders.product_id = products.id 
    WHERE orders.user_id = ? 
    ORDER BY orders.created_at DESC";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moj Profil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Moj Profil</h1>
    <a href="index.php" style="display: block; margin-bottom: 20px;">← Povratak na početnu stranicu</a>

    <!-- Success and Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_GET['success']) ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <!-- User Details -->
    <h2>Osnovni Podaci</h2>
    <p><strong>Korisničko ime:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Registrovan:</strong> <?= $user['created_at'] ?></p>

    <!-- User Orders -->
    <h2>Moje Narudžbe</h2>
    <?php if ($orders->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Narudžbe</th>
                    <th>Proizvod</th>
                    <th>Količina</th>
                    <th>Status</th>
                    <th>Datum</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td><?= ucfirst($order['status']) ?></td>
                        <td><?= $order['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nemate narudžbi.</p>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>

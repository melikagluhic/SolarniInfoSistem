<?php
// Start session
session_start();
require 'db/db_connection.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle order deletion
if (isset($_GET['delete_order'])) {
    $order_id = (int)$_GET['delete_order'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        $success_message = "Narudžba je uspješno obrisana.";
    } else {
        $error_message = "Greška pri brisanju narudžbe: " . $conn->error;
    }
}

// Fetch all orders
$query = "SELECT orders.id, users.username, products.name AS product_name, orders.quantity, orders.created_at 
          FROM orders 
          JOIN users ON orders.user_id = users.id 
          JOIN products ON orders.product_id = products.id 
          ORDER BY orders.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pregled Narudžbi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Pregled Narudžbi</h1>

    <!-- Display success or error messages -->
    <?php if (!empty($success_message)): ?>
        <p style="color: green; text-align: center;"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <p style="color: red; text-align: center;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Korisnik</th>
                <th>Proizvod</th>
                <th>Količina</th>
                <th>Datum</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a href="orders.php?delete_order=<?= $row['id'] ?>" onclick="return confirm('Jeste li sigurni da želite obrisati ovu narudžbu?');">Obriši</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

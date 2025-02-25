<?php
require 'db/db_connection.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detalji Narudžbe</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Detalji Narudžbe</h1>
    <p><strong>ID:</strong> <?= $order['id'] ?></p>
    <p><strong>Korisnik:</strong> <?= htmlspecialchars($order['user_id']) ?></p>
    <p><strong>Proizvod:</strong> <?= htmlspecialchars($order['product_id']) ?></p>
    <p><strong>Količina:</strong> <?= $order['quantity'] ?></p>
    <p><strong>Datum:</strong> <?= $order['created_at'] ?></p>
</body>
</html>

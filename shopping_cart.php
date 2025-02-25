<?php
// Start session
session_start();
require 'db/db_connection.php'; // Include database connection

// Initialize the shopping cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($product_id > 0 && $quantity > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
    header("Location: shopping_cart.php");
    exit;
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: shopping_cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaša Korpa</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Vaša Korpa</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Vaša korpa je prazna.</p>
        <a href="index.php">Nazad na proizvode</a>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Proizvod</th>
                    <th>Količina</th>
                    <th>Cijena</th>
                    <th>Ukupno</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $quantity):
                    $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $product = $result->fetch_assoc();

                    if ($product):
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= $quantity ?></td>
                    <td><?= number_format($product['price'], 2) ?> KM</td>
                    <td><?= number_format($subtotal, 2) ?> KM</td>
                    <td><a href="shopping_cart.php?remove=<?= $product_id ?>" onclick="return confirm('Jeste li sigurni da želite ukloniti ovaj proizvod?');">Ukloni</a></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Ukupno: <?= number_format($total, 2) ?> KM</h3>
        <a href="checkout.php">Nastavi na plaćanje</a>
    <?php endif; ?>
</body>
</html>

<?php
// Start session
session_start();
require 'db/db_connection.php'; // Include database connection

// Check if the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kompanija') {
    header("Location: login.php");
    exit;
}

// Fetch company products
$company_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE company_id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$products = $stmt->get_result();

// Fetch orders related to the company's products
$order_query = "
    SELECT orders.id, users.username AS customer, products.name AS product_name, 
           orders.quantity, orders.status, orders.created_at 
    FROM orders
    JOIN products ON orders.product_id = products.id
    JOIN users ON orders.user_id = users.id
    WHERE products.company_id = ?
    ORDER BY orders.created_at DESC";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kompanijski Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Dobrodošli u Kompanijski Panel</h1>
    <a href="logout.php" style="display: block; margin-bottom: 20px;">Logout</a>

    <!-- Company Products -->
    <h2>Vaši Proizvodi</h2>
    <?php if ($products->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Naziv</th>
                    <th>Opis</th>
                    <th>Cijena</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= number_format($product['price'], 2) ?> KM</td>
                        <td>
                            <a href="edit_product.php?id=<?= $product['id'] ?>">Uredi</a>
                            <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Jeste li sigurni da želite obrisati ovaj proizvod?');">Obriši</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Trenutno nemate proizvoda.</p>
    <?php endif; ?>

    <!-- Orders Related to Company Products -->
    <h2>Narudžbe za Vaše Proizvode</h2>
    <?php if ($orders->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kupac</th>
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
                        <td><?= htmlspecialchars($order['customer']) ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td><?= ucfirst($order['status']) ?></td>
                        <td><?= $order['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Trenutno nemate narudžbi.</p>
    <?php endif; ?>
</body>
</html>

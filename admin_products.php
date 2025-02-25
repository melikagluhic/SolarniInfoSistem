<?php
// Start session
session_start();
require 'db/db_connection.php'; // Include database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    if (empty($name) || $price <= 0 || empty($description)) {
        $error = "Sva polja su obavezna.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $name, $price, $description);
        if ($stmt->execute()) {
            $success = "Proizvod uspješno dodan.";
        } else {
            $error = "Greška pri dodavanju proizvoda.";
        }
    }
}

// Fetch existing products
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravljanje Proizvodima</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Upravljanje Proizvodima</h1>

    <!-- Poruke o uspjehu ili grešci -->
    <?php if (!empty($error)): ?>
        <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <p style="color: green; text-align: center;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <!-- Add Product Form -->
    <form method="POST" style="max-width: 600px; margin: 20px auto;">
        <h2>Dodaj Proizvod</h2>
        <label for="name">Naziv proizvoda:</label>
        <input type="text" name="name" id="name" required>
        <label for="price">Cijena:</label>
        <input type="number" name="price" id="price" step="0.01" required>
        <label for="description">Opis:</label>
        <textarea name="description" id="description" required></textarea>
        <button type="submit">Dodaj Proizvod</button>
    </form>

    <!-- List Existing Products -->
    <h2>Lista Proizvoda</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Naziv</th>
                <th>Cijena</th>
                <th>Opis</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $products->fetch_assoc()): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 2) ?> KM</td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id'] ?>">Uredi</a>
                        <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Jeste li sigurni da želite obrisati ovaj proizvod?');">Obriši</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

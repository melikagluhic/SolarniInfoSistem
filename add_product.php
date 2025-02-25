<?php
// Start session
session_start();
require 'db/db_connection.php';

// Check if the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kompanija') {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $company_id = $_SESSION['user_id'];

    if (empty($name) || empty($description) || $price <= 0) {
        $error = "Sva polja su obavezna i cijena mora biti veća od 0.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, company_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $name, $description, $price, $company_id);
        if ($stmt->execute()) {
            $success = "Proizvod uspješno dodan!";
        } else {
            $error = "Greška prilikom dodavanja proizvoda.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj Proizvod</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Dodaj Novi Proizvod</h1>
    <a href="company_dashboard.php" style="display: block; text-align: center; margin-bottom: 20px;">← Povratak na Kompanijski Panel</a>

    <!-- Display success or error messages -->
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <!-- Add Product Form -->
    <form method="POST" action="add_product.php" style="max-width: 600px; margin: 20px auto;">
        <label for="name">Naziv proizvoda:</label>
        <input type="text" name="name" id="name" required>
        <label for="description">Opis proizvoda:</label>
        <textarea name="description" id="description" required></textarea>
        <label for="price">Cijena (KM):</label>
        <input type="number" name="price" id="price" step="0.01" required>
        <button type="submit">Dodaj Proizvod</button>
    </form>
</body>
</html>

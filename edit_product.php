<?php
// Start session
session_start();
require 'db/db_connection.php'; // Include database connection

// Check if the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kompanija') {
    header("Location: login.php");
    exit;
}

// Get product ID from query parameter
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details and verify ownership
$company_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $product_id, $company_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Proizvod nije pronađen ili nemate dozvolu za uređivanje ovog proizvoda.");
}

// Handle product update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    if (empty($name) || $price <= 0 || empty($description)) {
        $error = "Sva polja su obavezna.";
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("sdsii", $name, $price, $description, $product_id, $company_id);
        if ($stmt->execute()) {
            $success = "Proizvod uspješno ažuriran.";
        } else {
            $error = "Greška pri ažuriranju proizvoda.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uredi Proizvod</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Uredi Proizvod</h1>
    <a href="company_dashboard.php" style="display: block; margin-bottom: 20px;">← Povratak na Kompanijski Panel</a>

    <form method="POST" style="max-width: 600px; margin: 20px auto;">
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <p style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <label for="name">Naziv proizvoda:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        <label for="price">Cijena (KM):</label>
        <input type="number" name="price" id="price" step="0.01" value="<?= $product['price'] ?>" required>
        <label for="description">Opis proizvoda:</label>
        <textarea name="description" id="description" required><?= htmlspecialchars($product['description']) ?></textarea>
        <button type="submit">Spremi Promjene</button>
    </form>
</body>
</html>

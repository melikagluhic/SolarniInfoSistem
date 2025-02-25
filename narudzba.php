<?php
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen kao klijent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'klijent') {
    header("Location: login.php");
    exit;
}

// Provjera ID proizvoda
if (!isset($_GET['product_id'])) {
    echo "Greška: ID proizvoda nije naveden.";
    exit;
}

$product_id = $_GET['product_id'];
$user_id = $_SESSION['user_id'];

// Unos narudžbe koristeći pripremljenu izjavu
$stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price) 
                        SELECT ?, id, 1, price FROM products WHERE id = ?");
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    echo "<p style='color: green;'>Narudžba je uspješno kreirana!</p>";
} else {
    echo "<p style='color: red;'>Greška prilikom kreiranja narudžbe: " . $stmt->error . "</p>";
}

$stmt->close();
?>
<br>
<a href="klijent.php">Nazad na proizvode</a>

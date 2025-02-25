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

// Check if product exists and belongs to the logged-in company
$company_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM products WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $product_id, $company_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: company_dashboard.php?error=Proizvod nije pronađen ili nemate dozvolu za brisanje.");
    exit;
}

// Delete product from database
$stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $product_id, $company_id);

if ($stmt->execute()) {
    header("Location: company_dashboard.php?success=Proizvod uspješno obrisan.");
    exit;
} else {
    header("Location: company_dashboard.php?error=Greška pri brisanju proizvoda.");
    exit;
}
?>

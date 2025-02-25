<?php
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen kao administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrator') {
    header("Location: login.php");
    exit;
}

// Dohvatanje ključnih informacija
$total_products_sql = "SELECT COUNT(*) AS total_products FROM products";
$total_products_result = $conn->query($total_products_sql);
$total_products = $total_products_result->fetch_assoc()['total_products'];

$total_companies_sql = "SELECT COUNT(*) AS total_companies FROM users WHERE role = 'kompanija'";
$total_companies_result = $conn->query($total_companies_sql);
$total_companies = $total_companies_result->fetch_assoc()['total_companies'];

$total_clients_sql = "SELECT COUNT(*) AS total_clients FROM users WHERE role = 'klijent'";
$total_clients_result = $conn->query($total_clients_sql);
$total_clients = $total_clients_result->fetch_assoc()['total_clients'];

$total_orders_sql = "SELECT COUNT(*) AS total_orders FROM orders";
$total_orders_result = $conn->query($total_orders_sql);
$total_orders = $total_orders_result->fetch_assoc()['total_orders'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Administrativni izvještaj</title>
</head>
<body>
    <h1>Izvještaj za administratora</h1>
    <table border="1">
        <tr>
            <th>Ukupan broj proizvoda</th>
            <td><?php echo $total_products; ?></td>
        </tr>
        <tr>
            <th>Ukupan broj kompanija</th>
            <td><?php echo $total_companies; ?></td>
        </tr>
        <tr>
            <th>Ukupan broj klijenata</th>
            <td><?php echo $total_clients; ?></td>
        </tr>
        <tr>
            <th>Ukupan broj narudžbi</th>
            <td><?php echo $total_orders; ?></td>
        </tr>
    </table>

    <br>
    <a href="dashboard.php">Nazad na dashboard</a>
</body>
</html>

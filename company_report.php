<?php
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen kao kompanija
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'kompanija') {
    header("Location: login.php");
    exit;
}

$company_id = $_SESSION['user_id'];

// Dohvatanje ključnih informacija o narudžbama i proizvodima kompanije
$total_company_products_sql = "SELECT COUNT(*) AS total_company_products FROM products WHERE company_id = '$company_id'";
$total_company_products_result = $conn->query($total_company_products_sql);
$total_company_products = $total_company_products_result->fetch_assoc()['total_company_products'];

$total_company_orders_sql = "SELECT COUNT(*) AS total_company_orders FROM orders 
                             INNER JOIN products ON orders.product_id = products.id 
                             WHERE products.company_id = '$company_id'";
$total_company_orders_result = $conn->query($total_company_orders_sql);
$total_company_orders = $total_company_orders_result->fetch_assoc()['total_company_orders'];

$pending_orders_sql = "SELECT COUNT(*) AS pending_orders FROM orders 
                       INNER JOIN products ON orders.product_id = products.id 
                       WHERE products.company_id = '$company_id' AND orders.status = 'pending'";
$pending_orders_result = $conn->query($pending_orders_sql);
$pending_orders = $pending_orders_result->fetch_assoc()['pending_orders'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Izvještaj za kompaniju</title>
</head>
<body>
    <h1>Izvještaj za vašu kompaniju</h1>
    <table border="1">
        <tr>
            <th>Ukupan broj proizvoda</th>
            <td><?php echo $total_company_products; ?></td>
        </tr>
        <tr>
            <th>Ukupan broj narudžbi</th>
            <td><?php echo $total_company_orders; ?></td>
        </tr>
        <tr>
            <th>Broj narudžbi na čekanju</th>
            <td><?php echo $pending_orders; ?></td>
        </tr>
    </table>

    <br>
    <a href="kompanija.php">Nazad na dashboard kompanije</a>
</body>
</html>

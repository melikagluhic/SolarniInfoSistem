<?php
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen kao kompanija
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'kompanija') {
    header("Location: login.php");
    exit;
}

$company_id = $_SESSION['user_id'];

// Dohvatanje narudžbi za proizvode kompanije
$sql = "SELECT orders.*, products.name AS product_name, users.username AS client_name 
        FROM orders 
        INNER JOIN products ON orders.product_id = products.id 
        INNER JOIN users ON orders.user_id = users.id
        WHERE products.company_id = '$company_id'";
$result = $conn->query($sql);

// Odobravanje ili odbijanje narudžbe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $sql_update = "UPDATE orders SET status = 'approved' WHERE id = '$order_id'";
    } elseif ($action == 'reject') {
        $sql_update = "UPDATE orders SET status = 'rejected' WHERE id = '$order_id'";
    }

    if ($conn->query($sql_update) === TRUE) {
        $success_message = "Narudžba je uspješno ažurirana.";
    } else {
        $error_message = "Greška prilikom ažuriranja narudžbe: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Narudžbe za proizvode</title>
</head>
<body>
    <h1>Narudžbe za vaše proizvode</h1>

    <!-- Poruke o uspjehu ili grešci -->
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Narudžba ID</th>
                <th>Proizvod</th>
                <th>Kupac</th>
                <th>Količina</th>
                <th>Ukupna cijena</th>
                <th>Status</th>
                <th>Datum narudžbe</th>
                <th>Akcije</th>
            </tr>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['client_name']); ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td><?php echo $order['total_price']; ?> KM</td>
                    <td><?php echo ucfirst($order['status']); ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td>
                        <?php if ($order['status'] == 'pending'): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" name="action" value="approve">Odobri</button>
                                <button type="submit" name="action" value="reject">Odbij</button>
                            </form>
                        <?php else: ?>
                            <?php echo ucfirst($order['status']); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Nema narudžbi za vaše proizvode.</p>
    <?php endif; ?>

    <br>
    <a href="kompanija.php">Nazad na dashboard kompanije</a> | <a href="logout.php">Odjava</a>
</body>
</html>

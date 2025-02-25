<?php
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Provjera ID proizvoda
if (!isset($_GET['id'])) {
    echo "Greška: ID proizvoda nije naveden.";
    exit;
}

$product_id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = '$product_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Proizvod nije pronađen.";
    exit;
}

$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Detalji proizvoda</title>
</head>
<body>
    <h2>Detalji proizvoda</h2>
    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Slika proizvoda" width="300"><br>
    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
    <p><?php echo htmlspecialchars($product['description']); ?></p>
    <p><strong>Cijena:</strong> <?php echo $product['price']; ?> KM</p>
    <p><strong>Snaga:</strong> <?php echo $product['power']; ?> kWh</p>
    <a href="narudzba.php?product_id=<?php echo $product['id']; ?>" onclick="return confirm('Da li želite naručiti ovaj proizvod?');">Naruči</a>
    <br><br>
    <a href="klijent.php">Nazad</a>
</body>
</html>

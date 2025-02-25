<?php
require_once 'db/db_connection.php';
session_start();

// Provjera korisničke sesije
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kompanija') {
    header('Location: ../login.php');
    exit();
}

// Prikaz proizvoda
while ($row = $result->fetch_assoc()) {
    echo "<div class='product'>";
    echo "<h3>{$row['name']}</h3>";
    echo "<img src='{$row['image']}' alt='{$row['name']}' />";
    echo "<p>Cijena: {$row['price']} KM</p>";
    echo "<p>{$row['description']}</p>";
    echo "<form action='order.php' method='post'>";
    echo "<input type='hidden' name='product_id' value='{$row['id']}'>";
    echo "<button type='submit'>Naruči</button>";
    echo "</form>";
    echo "</div>";
}

<?php
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    echo "<p style='color: green;'>Narudžba je uspješno kreirana!</p>";
}
?>

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $power = $_POST['power'];
    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $query = "INSERT INTO products (name, description, price, power, image, company_id)
                  VALUES ('$name', '$description', '$price', '$power', '$image', {$_SESSION['user_id']})";
        mysqli_query($conn, $query);
        echo "<p>Proizvod uspješno dodan!</p>";
    } else {
        echo "<p>Greška prilikom dodavanja slike!</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dodaj Proizvod</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>Dodaj novi proizvod</h2>
        <input type="text" name="name" placeholder="Naziv" required>
        <textarea name="description" placeholder="Opis" required></textarea>
        <input type="number" name="price" placeholder="Cijena (KM)" required>
        <input type="number" name="power" placeholder="Snaga (W)" required>
        <input type="file" name="image" required>
        <button type="submit">Dodaj proizvod</button>
    </form>
</body>
</html>

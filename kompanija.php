<?php
session_start();
require_once 'db/db_connection.php';

// Provjera je li korisnik prijavljen kao kompanija
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'kompanija') {
    header("Location: login.php");
    exit;
}

// Dodavanje proizvoda
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float) $_POST['price'];
    $power = (float) $_POST['power'];

    // Upload slike
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Kreiraj direktorijum ako ne postoji
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (name, description, price, power, image, company_id) 
                VALUES ('$name', '$description', $price, $power, '$target_file', '{$_SESSION['user_id']}')";
        if ($conn->query($sql) === TRUE) {
            $success_message = "Proizvod je uspješno dodan!";
        } else {
            $error_message = "Greška prilikom dodavanja proizvoda: " . $conn->error;
        }
    } else {
        $error_message = "Greška prilikom upload-a slike.";
    }
}

// Prikaz proizvoda kompanije
$company_id = $_SESSION['user_id'];
$sql = "SELECT * FROM products WHERE company_id = '$company_id'";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<script src="js/script.js"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Dashboard Kompanije</title>
</head>
<body>
    <h2>Dashboard Kompanije</h2>

    <!-- Poruke za uspjeh ili greške -->
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Forma za dodavanje proizvoda -->
    <h3>Dodavanje novog proizvoda</h3>
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Naziv proizvoda:</label>
        <input type="text" name="name" required><br>
        
        <label for="description">Opis proizvoda:</label>
        <textarea name="description" required></textarea><br>
        
        <label for="price">Cijena (KM):</label>
        <input type="number" step="0.01" name="price" required><br>
        
        <label for="power">Snaga (kWh):</label>
        <input type="number" step="0.01" name="power" required><br>
        
        <label for="image">Slika proizvoda:</label>
        <input type="file" name="image" accept="image/*" required><br>
        
        <button type="submit" name="add_product">Dodaj proizvod</button>
    </form>

    <!-- Lista proizvoda kompanije -->
    <h3>Vaši proizvodi</h3>
    <table border="1">
        <tr>
            <th>Naziv</th>
            <th>Opis</th>
            <th>Cijena</th>
            <th>Snaga (kWh)</th>
            <th>Slika</th>
            <th>Akcije</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo $product['price']; ?> KM</td>
                    <td><?php echo $product['power']; ?> kWh</td>
                    <td><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Slika" width="100"></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>">Izmijeni</a> |
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                           onclick="return confirm('Da li ste sigurni da želite obrisati ovaj proizvod?');">
                           Obriši
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Nema proizvoda.</td>
            </tr>
        <?php endif; ?>
    </table>

    <a href="logout.php">Odjava</a>
</body>
</html>

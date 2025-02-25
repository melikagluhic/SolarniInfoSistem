<?php
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen kao klijent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'klijent') {
    header("Location: login.php");
    exit;
}

// Pretraga proizvoda
$search_query = "";
$where_clause = "";

if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
}

// Paginacija
$limit = 5;  // broj proizvoda po stranici
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ukupan broj proizvoda
$count_sql = "SELECT COUNT(*) AS total FROM products $where_clause";
$count_result = $conn->query($count_sql);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Dohvatanje proizvoda sa limitom i offsetom
$sql = "SELECT * FROM products $where_clause LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Dohvatanje kategorija
$categories_result = $conn->query("SELECT * FROM categories");

$category_filter = "";
if (isset($_GET['category'])) {
    $category_filter = (int)$_GET['category'];
    $where_clause .= " AND category_id = '$category_filter'";
}

?>

<!-- Prikaz kategorija za filtriranje -->
<form method="GET" action="">
    <select name="category" onchange="this.form.submit()">
        <option value="">Sve kategorije</option>
        <?php while ($category = $categories_result->fetch_assoc()): ?>
            <option value="<?php echo $category['id']; ?>" <?php if ($category_filter == $category['id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

?>
<!DOCTYPE html>
<html lang="en">
<script src="js/script.js"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Proizvodi</title>
</head>
<body>
    <h1>Dobrodošli, <?php echo $_SESSION['username']; ?>!</h1>
    <h2>Pregled proizvoda</h2>

    <!-- Forma za pretragu -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Pretraži proizvode..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Pretraži</button>
    </form>
    <br>

    <!-- Prikaz proizvoda -->
    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Slika</th>
                <th>Naziv</th>
                <th>Opis</th>
                <th>Cijena</th>
                <th>Snaga</th>
                <th>Akcije</th>
            </tr>
            <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Slika" width="100"></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo $product['price']; ?> KM</td>
                    <td><?php echo $product['power']; ?> kWh</td>
                    <td>
                        <a href="product_details.php?id=<?php echo $product['id']; ?>">Detalji</a> | 
                        <a href="narudzba.php?product_id=<?php echo $product['id']; ?>" onclick="return confirm('Da li želite naručiti ovaj proizvod?');">Naruči</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Paginacija -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo $search_query; ?>">Prethodna</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo $search_query; ?>" <?php if ($i == $page) echo 'style="font-weight: bold;"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo $search_query; ?>">Sljedeća</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Nema proizvoda koji odgovaraju vašoj pretrazi.</p>
    <?php endif; ?>

    <br>
    <a href="logout.php">Odjava</a>
</body>
</html>

<?php
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen kao administrator
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Provjera ID korisnika
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Greška: Nevažeći ID korisnika.";
    exit;
}

$user_id = intval($_GET['id']);

// Dohvati korisničke podatke
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Korisnik nije pronađen.";
    exit;
}

$user = $result->fetch_assoc();

// Ažuriranje korisnika
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);

    // Validacija ulaza
    $valid_roles = ['klijent', 'kompanija', 'administrator'];
    if (empty($username) || !in_array($role, $valid_roles)) {
        echo "<p style='color: red;'>Nevažeći unos. Provjerite korisničko ime i ulogu.</p>";
    } else {
        // Ažuriraj podatke u bazi
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $role, $user_id);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Korisnički podaci su uspješno ažurirani. <a href='admin_users.php'>Nazad na listu korisnika</a></p>";
            exit;
        } else {
            echo "<p style='color: red;'>Greška prilikom ažuriranja korisnika: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Izmjena korisnika</title>
</head>
<body>
    <h2>Izmjena korisničkih podataka</h2>
    <form method="POST" action="">
        <label for="username">Korisničko ime:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>

        <label for="role">Uloga:</label>
        <select name="role" required>
            <option value="klijent" <?php if ($user['role'] === 'klijent') echo 'selected'; ?>>Klijent</option>
            <option value="kompanija" <?php if ($user['role'] === 'kompanija') echo 'selected'; ?>>Kompanija</option>
            <option value="administrator" <?php if ($user['role'] === 'administrator') echo 'selected'; ?>>Administrator</option>
        </select><br><br>

        <button type="submit">Ažuriraj korisnika</button>
    </form>

    <br>
    <a href="admin_users.php">Nazad na listu korisnika</a>
</body>
</html>

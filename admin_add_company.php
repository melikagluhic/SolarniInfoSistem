<?php
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen kao administrator
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Dodavanje kompanije
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validacija unosa
    if (empty($username) || empty($password)) {
        $error = "Sva polja su obavezna.";
    } elseif (strlen($password) < 6) {
        $error = "Šifra mora sadržavati najmanje 6 karaktera.";
    } else {
        // Provjera da li korisničko ime već postoji
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Korisničko ime već postoji.";
        } else {
            // Dodavanje kompanije u bazu
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'kompanija')");
            $stmt->bind_param("ss", $username, $password_hash);

            if ($stmt->execute()) {
                $success = "Kompanija je uspješno dodana!";
            } else {
                $error = "Greška prilikom dodavanja kompanije: " . $conn->error;
            }
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
    <title>Dodavanje kompanije</title>
</head>
<body>
    <h2>Dodavanje nove kompanije</h2>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="username">Korisničko ime:</label>
        <input type="text" name="username" required><br>
        
        <label for="password">Šifra:</label>
        <input type="password" name="password" required><br>
        
        <button type="submit">Dodaj kompaniju</button>
    </form>

    <br>
    <a href="dashboard.php">Nazad na dashboard</a>
</body>
</html>

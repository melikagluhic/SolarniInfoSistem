<?php
// Start session
session_start();
require 'db/db_connection.php';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']); // Tip korisnika (client ili company)

    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "Sva polja su obavezna.";
    } else {
        // Provjera da li korisničko ime ili email već postoje
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Korisničko ime ili email već postoje.";
        } else {
            // Dodavanje novog korisnika
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password, $role);
            if ($stmt->execute()) {
                $success = "Registracija uspješna! Možete se prijaviti.";
            } else {
                $error = "Greška: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registracija</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="POST" action="register.php">
        <h2>Registracija</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <p style="color: green;"><?= $success ?></p>
        <?php endif; ?>
        <label for="username">Korisničko ime:</label>
        <input type="text" name="username" id="username" required>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Lozinka:</label>
        <input type="password" name="password" id="password" required>
        <label for="role">Uloga:</label>
        <select name="role" id="role" required>
            <option value="client">Klijent</option>
            <option value="company">Kompanija</option>
        </select>
        <button type="submit">Registriraj se</button>
    </form>
</body>
</html>

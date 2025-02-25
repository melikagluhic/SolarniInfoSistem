<?php
require_once 'db/db_connection.php';

// Provjera unosa korisnika i resetovanje lozinke
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = $conn->real_escape_string($_POST['email_or_username']);
    $new_password = $conn->real_escape_string($_POST['new_password']);

    // Dohvati korisnika putem e-maila ili korisničkog imena
    $sql = "SELECT * FROM users WHERE email = '$email_or_username' OR username = '$email_or_username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Ažuriraj lozinku korisnika
        $sql_update = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
        if ($conn->query($sql_update) === TRUE) {
            echo "<p style='color: green;'>Lozinka je uspješno ažurirana! Možete se prijaviti koristeći novu lozinku. <a href='login.php'>Prijava</a></p>";
        } else {
            echo "<p style='color: red;'>Greška prilikom ažuriranja lozinke: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Korisnik sa unesenim podacima nije pronađen.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Resetovanje lozinke</title>
</head>
<body>
    <h2>Resetovanje lozinke</h2>
    <form method="POST" action="">
        <label for="email_or_username">Unesite e-mail ili korisničko ime:</label>
        <input type="text" name="email_or_username" required><br>

        <label for="new_password">Nova lozinka:</label>
        <input type="password" name="new_password" required><br><br>

        <button type="submit">Resetuj lozinku</button>
    </form>

    <br>
    <a href="login.php">Nazad na prijavu</a>
</body>
</html>

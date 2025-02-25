<?php
// Start session
session_start();
require 'db/db_connection.php'; // Connect to the database

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input values
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = "Sva polja su obavezna.";
    } else {
        // Check if the user is an admin
        $adminQuery = "SELECT id, password FROM admins WHERE username = ?";
        $stmt = $conn->prepare($adminQuery);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Admin login
            $admin = $result->fetch_assoc();
            if (hash('sha256', $password) === $admin['password']) {
                $_SESSION['admin_id'] = $admin['id'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Pogrešno korisničko ime ili lozinka.";
            }
        } else {
            // Check if the user is a regular user
            $userQuery = "SELECT id, password, role FROM users WHERE username = ?";
            $stmt = $conn->prepare($userQuery);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (hash('sha256', $password) === $user['password']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    if ($user['role'] === 'klijent') {
                        header("Location: user_profile.php");
                    } elseif ($user['role'] === 'kompanija') {
                        header("Location: company_dashboard.php");
                    }
                    exit;
                } else {
                    $error = "Pogrešno korisničko ime ili lozinka.";
                }
            } else {
                $error = "Pogrešno korisničko ime ili lozinka.";
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
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="POST" action="login.php">
        <h2>Prijava</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <label for="username">Korisničko ime:</label>
        <input type="text" name="username" id="username" required>
        <label for="password">Lozinka:</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Prijava</button>
    </form>
</body>
</html>

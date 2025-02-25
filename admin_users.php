<?php
// Start session
session_start();
require_once 'db/db_connection.php';

// Provjera da li je korisnik prijavljen kao administrator
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Brisanje korisnika
if (isset($_GET['delete_user'])) {
    $user_id = (int)$_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $success_message = "Korisnik je uspješno obrisan.";
    } else {
        $error_message = "Greška prilikom brisanja korisnika: " . $conn->error;
    }
}

// Dohvatanje svih korisnika
$sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Administracija korisnika</title>
</head>
<body>
    <h1>Administracija korisnika</h1>

    <!-- Poruke o uspjehu ili grešci -->
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Korisničko ime</th>
                <th>Email</th>
                <th>Uloga</th>
                <th>Registrovan</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>">Izmijeni</a> | 
                        <a href="admin_users.php?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Da li ste sigurni da želite obrisati ovog korisnika?');">Obriši</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <div style="text-align: center;">
        <a href="admin_add_company.php">Dodaj novu kompaniju</a> | <a href="logout.php">Odjava</a>
    </div>
</body>
</html>

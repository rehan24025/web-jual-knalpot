<?php
session_start();
include '../includes/db.php'; // Include database connection file

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'user'; // Default role is 'user'

    // Cek apakah username sudah ada
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika username sudah ada, berikan pesan error
        echo "<script>alert('Username sudah dipakai, coba yang lain.');</script>";
    } else {
        // Jika username belum ada, masukkan ke database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            // Redirect ke login.php setelah registrasi berhasil
            header("Location: login.php");
            exit(); // Pastikan script berhenti setelah redirect
        } else {
            echo "<script>alert('Terjadi kesalahan: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Knolpot Racing</title>
    <link rel="stylesheet" href="../css/auth.css"> <!-- Link to CSS file -->
</head>
<body>

<div class="container">
    <h2>Register</h2>

    <form action="" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Register</button>
    </form>

    <a href="login.php">Kamu sudah memiliki akun? Login di sini</a>
</div>

</body>
</html>

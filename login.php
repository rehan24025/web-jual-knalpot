<?php
session_start();
include '../includes/db.php'; // Include database connection file

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data pengguna berdasarkan username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Set cookies untuk login (berlaku selama 7 hari)
            setcookie('user_id', $user['id_user'], time() + (86400 * 7), "/"); // Simpan user_id di cookies
            setcookie('username', $user['username'], time() + (86400 * 7), "/"); // Simpan username di cookies
            setcookie('role', $user['role'], time() + (86400 * 7), "/"); // Simpan role di cookies

            // Redirect berdasarkan role pengguna
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit(); // Pastikan untuk menghentikan eksekusi skrip setelah redirect
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Knolpot Racing</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>

      
          <a href="register.php">Kamu tidak punya akun? Register di sini</a>
</div>

</body>
</html>

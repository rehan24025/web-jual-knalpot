<?php
session_start();
include '../includes/db.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Ambil permintaan reset password yang pending
$sql = "SELECT * FROM password_resets WHERE status = 'pending'";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $username = $_POST['username'];
    $default_password = password_hash('123', PASSWORD_DEFAULT);

    // Update password pengguna
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $default_password, $username);
    $stmt->execute();

    // Ubah status permintaan menjadi 'completed'
    $stmt = $conn->prepare("UPDATE password_resets SET status = 'completed' WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Set pesan sukses dalam sesi
    $_SESSION['reset_success'] = "Password untuk $username telah direset ke default (PW: 123).";
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh halaman untuk menampilkan pesan
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Permintaan Reset Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        header {
            background-color: #343a40;
            color: white;
            padding: 15px;
        }
        header a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }
        header a:hover {
            text-decoration: underline;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h4 m-0">Kelola Permintaan Reset Password</h1>
        <nav>
            <a href="../admin/dashboard.php">Dashboard</a>
            <a href="../admin/manage_users.php">Kelola Pengguna</a>
            <a href="../admin/logout.php">Logout</a>
        </nav>
    </div>
</header>

<div class="container mt-4">
    <h2 class="mb-3">Daftar Permintaan Reset Password</h2>

    <!-- Pesan Sukses -->
    <?php if (isset($_SESSION['reset_success'])): ?>
        <div class="success-message" id="success-message">
            <?php echo $_SESSION['reset_success']; ?>
        </div>
        <?php unset($_SESSION['reset_success']); ?>
    <?php endif; ?>

    <!-- Tabel Permintaan Reset -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Username</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                        <form action="" method="post">
                            <input type="hidden" name="username" value="<?php echo $row['username']; ?>">
                            <button type="submit" name="reset_password" class="btn btn-primary btn-sm">Reset Password</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

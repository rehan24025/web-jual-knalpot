<?php
session_start();
include '../includes/db.php';

// Dapatkan ID pengguna dari URL
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

// Validasi ID pengguna
if (!$user_id) {
    echo "<script>
        alert('ID pengguna tidak valid.');
        window.location.href = 'manage_users.php';
    </script>";
    exit();
}

// Ambil data pengguna
$stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
        alert('Pengguna tidak ditemukan.');
        window.location.href = 'manage_users.php';
    </script>";
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();

// Proses pembaruan atau penghapusan pengguna jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) { // Tombol update ditekan
        $username = $_POST['username'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id_user = ?");
        $stmt->bind_param("ssi", $username, $role, $user_id);

        if ($stmt->execute()) {
            echo "<script>
                alert('Pengguna berhasil diperbarui!');
                window.location.href = 'manage_users.php';
            </script>";
            exit();
        } else {
            echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
                window.location.href = 'manage_users.php';
            </script>";
        }
        $stmt->close();
    } elseif (isset($_POST['delete'])) { // Tombol delete ditekan
        // Hapus pengguna dari tabel users
        $stmt = $conn->prepare("DELETE FROM users WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            echo "<script>
                alert('Pengguna dan data terkait berhasil dihapus.');
                window.location.href = 'manage_users.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
                window.location.href = 'manage_users.php';
            </script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Pengguna</h2>
    
    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="alert alert-success text-center">
            Pengguna berhasil diperbarui!
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <!-- Input untuk Nama Pengguna -->
        <div class="mb-3">
            <label for="username" class="form-label">Nama:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?>" required>
        </div>

        <!-- Radio button untuk Role -->
        <div class="mb-3">
            <label class="form-label">Role:</label>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="role" value="user" <?php echo ($row['role'] == 'user') ? 'checked' : ''; ?>>
                <label class="form-check-label">User</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="role" value="admin" <?php echo ($row['role'] == 'admin') ? 'checked' : ''; ?>>
                <label class="form-check-label">Admin</label>
            </div>
        </div>

        <!-- Tombol untuk Update dan Hapus -->
        <button type="submit" name="update" class="btn btn-success w-100 mb-3">Update Pengguna</button>
    </form>

    <div class="mt-3 text-center">
        <a href="manage_users.php" class="btn btn-secondary">Kembali ke Daftar Pengguna</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

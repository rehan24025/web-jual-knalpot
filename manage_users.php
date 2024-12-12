<?php
session_start();
include '../includes/db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        header {
            background-color: #343a40;
            color: white;
            padding: 15px;
        }
        header h1 {
            margin: 0;
        }
        header nav ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            gap: 15px;
        }
        header nav ul li a {
            text-decoration: none;
            color: white;
            transition: color 0.3s ease;
        }
        header nav ul li a:hover {
            color: #adb5bd;
        }
        .success-popup {
            display: none;
            background-color: #28a745;
            color: white;
            text-align: center;
            padding: 10px 20px;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            border-radius: 4px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.2);
        }
        .success-popup.active {
            display: block;
            animation: fade-in-out 3s ease-in-out;
        }
        @keyframes fade-in-out {
            0%, 100% { opacity: 0; }
            10%, 90% { opacity: 1; }
        }
    </style>
</head>
<body>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <h1>Kelola Pengguna</h1>
        <nav>
            <ul class="d-flex gap-3">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_products.php">Kelola Produk</a></li>
                <li><a href="manage_orders.php">Kelola Pesanan</a></li>
                <li><a href="../user/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container mt-4">
    <h2 class="mb-3">Daftar Pengguna</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID Pengguna</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_user']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $row['id_user']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_user.php?id=<?php echo $row['id_user']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pop-up berhasil -->
<div class="success-popup" id="success-popup">
    Pengguna berhasil diperbarui!
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Display success pop-up if the URL contains success=true
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        const popup = document.getElementById('success-popup');
        popup.classList.add('active');
        setTimeout(() => {
            popup.classList.remove('active');
        }, 3000); // Ubah durasi sesuai kebutuhan
    }
</script>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit();
}

include '../includes/db.php';

// Ambil semua tipe produk
$sql_tipe = "SELECT * FROM tipe";
$result_tipe = $conn->query($sql_tipe);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tipe Produk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        header {
            background-color: #343a40; /* Warna latar belakang */
            color: white;
            padding: 15px 20px;
        }
        header .navbar-nav .nav-link {
            color: white;
            transition: color 0.3s ease;
        }
        header .navbar-nav .nav-link:hover {
            color: #adb5bd; /* Warna hover */
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <h1 class="navbar-brand m-0">Kelola Tipe Produk</h1>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Kelola Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="../user/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="container mt-4">
    <h2>Daftar Tipe Produk</h2>
    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID Tipe</th>
                <th>Nama Tipe</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_tipe->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_tipe']); ?></td>
                    <td><?= htmlspecialchars($row['nama_tipe']); ?></td>
                    <td>
                        <a href="edit_tipe.php?id=<?= htmlspecialchars($row['id_tipe']); ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete_tipe.php?id=<?= htmlspecialchars($row['id_tipe']); ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Yakin ingin menghapus tipe ini?')">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="add_tipe.php" class="btn btn-success">Tambah Tipe Baru</a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

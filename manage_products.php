<?php
session_start();

// Cek apakah user sudah login dan admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit();
}

include '../includes/db.php';

// Ambil data tipe produk dan produk
$sql_produk = "SELECT produk.*, tipe.nama_tipe 
               FROM produk 
               LEFT JOIN tipe ON produk.id_tipe = tipe.id_tipe";
$result_produk = $conn->query($sql_produk);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
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
        table img {
            border-radius: 5px;
        }
        .container {
            margin: 20px auto;
            max-width: 1200px;
        }
        table {
            font-size: 14px;
        }
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <h1>Kelola Produk</h1>
        <nav>
            <ul class="d-flex gap-3">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_orders.php">Kelola Pesanan</a></li>
                <li><a href="manage_users.php">Kelola Pengguna</a></li>
                <li><a href="manage_tipe.php">Kelola Tipe</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container mt-4">
    <h2 class="mb-3">Daftar Produk</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Tipe</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php 
$i = 1; // Inisialisasi penghitung nomor urut
while ($row = $result_produk->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $i++; // Tampilkan nomor urut ?></td>
        <td><img src="../uploads/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>" width="50" height="50"></td>
        <td><?php echo $row['nama_produk']; ?></td>
        <td><?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
        <td><?php echo $row['stok']; ?></td>
        <td><?php echo $row['nama_tipe']; ?></td>
        <td><?php echo $row['deskripsi']; ?></td>
        <td>
            <a href="edit_product.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_product.php?id_produk=<?php echo $row['id_produk']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
        </td>
    </tr>
<?php } ?>

            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <a href="add_product.php" class="btn btn-primary">Tambah Produk Baru</a>
        <a href="add_tipe.php" class="btn btn-secondary">Tambah Tipe Produk Baru</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

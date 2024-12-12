<?php
session_start();
include_once '../includes/db.php';

// Handle search functionality
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// Adjust query based on search term
$query = "SELECT * FROM orders WHERE kode_transaksi LIKE ?";
$stmt = $conn->prepare($query);
$search_param = '%' . $search_term . '%';  // Add wildcard for partial matching
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        header {
            background-color: #343a40;
            color: white;
            padding: 10px 20px;
        }
        header h1 {
            margin: 0;
        }
        header nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
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
    <h2>Manage Orders</h2>

    <?php
    // Menampilkan notifikasi jika ada
    if (isset($_SESSION['message'])) {
        echo "
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
            {$_SESSION['message']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";
        unset($_SESSION['message']);
    }
    ?>

    <!-- Search Form -->
    <form method="get" action="manage_orders.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari Kode Transaksi" value="<?php echo htmlspecialchars($search_term); ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID Order</th>
                    <th>Kode Transaksi</th>
                    <th>ID User</th>
                    <th>Total Price</th>
                    <th>Status Order</th>
                </tr>
            </thead>
            <tbody>
            <?php 
$i = 1; // Inisialisasi variabel penghitung untuk nomor urut
while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $i++; // Tampilkan nomor urut ?></td>
        <td><?php echo htmlspecialchars($row['kode_transaksi']); ?></td>
        <td><?php echo htmlspecialchars($row['id_user']); ?></td>
        <td>Rp<?php echo number_format($row['total_price'], 2, '.', '.'); ?></td>
        <td>
            <form method="post" action="update_order.php" class="d-inline">
                <select name="status_order" class="form-select form-select-sm d-inline w-auto">
                    <option value="pending" <?php echo ($row['status_order'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="proses" <?php echo ($row['status_order'] == 'proses') ? 'selected' : ''; ?>>Proses</option>
                    <option value="dibatalkan" <?php echo ($row['status_order'] == 'dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                    <option value="selesai" <?php echo ($row['status_order'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                </select>
                <input type="hidden" name="id_order" value="<?php echo htmlspecialchars($row['id_order']); ?>">
                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                <a href="delete_order.php?id_order=<?php echo htmlspecialchars($row['id_order']); ?>" 
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Apakah Anda yakin ingin menghapus order ini?');">Hapus</a>
            </form>
        </td>
    </tr>
<?php endwhile; ?>

            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

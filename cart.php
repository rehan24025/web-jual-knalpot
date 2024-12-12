<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../includes/db.php'; // Pastikan koneksi database sudah di-include

// Notifikasi jika ada pesan dari sesi
if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-success text-center' style='margin-top:10px;'>"
        . htmlspecialchars($_SESSION['message']) .
        "</div>";
    unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
}

// Periksa apakah keranjang kosong
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "
    <div class='empty-cart text-center' style='margin-top:50px;'>
        <p>Keranjang Anda kosong.</p>
        <a href='../product.php' class='btn btn-primary'>Kembali ke Produk</a>
    </div>";
    exit();
}

// Proses aksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['place_order'])) {
        // Cek jika ada item yang dipilih
        if (!isset($_POST['selected_items']) || empty($_POST['selected_items'])) {
            $_SESSION['message'] = 'Silakan pilih item yang ingin dipesan.';
            header('Location: cart.php');
            exit();
        }

        $id_user = $_SESSION['user_id'];
        $selected_items = $_POST['selected_items'];
        $total_price = 0;

        foreach ($selected_items as $key) {
            if (isset($_SESSION['cart'][$key])) {
                $id_produk = $_SESSION['cart'][$key]['id_produk'];
                $quantity = $_SESSION['cart'][$key]['quantity'];

                // Ambil stok dari database
                $query = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
                $query->bind_param("i", $id_produk);
                $query->execute();
                $result = $query->get_result();
                $stok_db = $result->fetch_assoc()['stok'];

                // Validasi stok
                if ($quantity > $stok_db) {
                    $_SESSION['message'] = 'Stok tidak mencukupi untuk produk ' . $_SESSION['cart'][$key]['nama_produk'];
                    header('Location: cart.php');
                    exit();
                }

                // Hitung total harga
                $total_price += $quantity * $_SESSION['cart'][$key]['harga'];
            }
        }

        // Insert order
        $kode_transaksi = uniqid('TRX-');
        $insert_order = $conn->prepare("INSERT INTO orders (id_user, kode_transaksi, total_price, status_order, order_date) VALUES (?, ?, ?, 'pending', NOW())");
        $insert_order->bind_param("isi", $id_user, $kode_transaksi, $total_price);

        if ($insert_order->execute()) {
            $id_order = $insert_order->insert_id;

            foreach ($selected_items as $key) {
                if (isset($_SESSION['cart'][$key])) {
                    $id_produk = $_SESSION['cart'][$key]['id_produk'];
                    $quantity = $_SESSION['cart'][$key]['quantity'];
                    $harga = $_SESSION['cart'][$key]['harga'];

                    // Kurangi stok
                    $update_stock = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
                    $update_stock->bind_param("ii", $quantity, $id_produk);
                    $update_stock->execute();

                    // Tambahkan ke order_items
                    $insert_item = $conn->prepare("INSERT INTO order_items (id_order, id_produk, quantity, harga) VALUES (?, ?, ?, ?)");
                    $insert_item->bind_param("iiii", $id_order, $id_produk, $quantity, $harga);
                    $insert_item->execute();

                    unset($_SESSION['cart'][$key]); // Hapus item dari keranjang
                }
            }

            $_SESSION['message'] = 'Pesanan berhasil dibuat.';
            header('Location: order_success.php');
            exit();
        } else {
            echo "Error: " . $insert_order->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-center mb-4">Keranjang Belanja</h2>
    <form method="post" action="cart.php">
        <div class="row">
            <?php foreach ($_SESSION['cart'] as $key => $item): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="../uploads/<?php echo htmlspecialchars($item['gambar']); ?>" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['nama_produk']); ?></h5>
                            <p class="card-text">Harga: Rp<?php echo number_format($item['harga'], 2, '.', '.'); ?></p>
                            <p class="card-text">Jumlah: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            <input type="checkbox" name="selected_items[]" value="<?php echo $key; ?>"> Pilih
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" name="place_order" class="btn btn-success">Beli Sekarang</button>
            <a href="../product.php" class="btn btn-primary">Kembali</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

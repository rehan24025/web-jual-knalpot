<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Anda harus login untuk melakukan pemesanan.';
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$id_produk = $_GET['id_produk'];
$query = $conn->prepare("SELECT * FROM produk WHERE id_produk = ?");
$query->bind_param("i", $id_produk);
$query->execute();
$result = $query->get_result();
$produk = $result->fetch_assoc();

// Fungsi untuk memesan produk
if (isset($_POST['order_now'])) {
    $quantity = (int)$_POST['quantity'];

    if ($produk['stok'] >= $quantity) {
        // Menghasilkan kode transaksi unik
        $kode_transaksi = "TRX-" . date('Ymd') . "-" . uniqid();

        // Menghitung total harga
        $total_price = $produk['harga'] * $quantity;

        // Insert ke tabel orders
        $insert_order = $conn->prepare("INSERT INTO orders (id_user, total_price, status_order, kode_transaksi) VALUES (?, ?, 'pending', ?)");
        $insert_order->bind_param("iis", $id_user, $total_price, $kode_transaksi);

        if ($insert_order->execute()) {
            // Ambil ID order yang baru dimasukkan
            $id_order = $insert_order->insert_id;

            // Insert ke tabel order_items
            $insert_item = $conn->prepare("INSERT INTO order_items (id_order, id_produk, quantity) VALUES (?, ?, ?)");
            $insert_item->bind_param("iii", $id_order, $id_produk, $quantity);
            $insert_item->execute();

            // Pengurangan stok produk
            $stok_baru = $produk['stok'] - $quantity;
            $update_stok = $conn->prepare("UPDATE produk SET stok = ? WHERE id_produk = ?");
            $update_stok->bind_param("ii", $stok_baru, $id_produk);
            $update_stok->execute();

            // Redirect ke halaman sukses setelah pemesanan
            header("Location: order_success.php");
            exit();
        } else {
            echo "Error: " . $insert_order->error;
        }
    } else {
        $_SESSION['message'] = 'Stok tidak mencukupi untuk pesanan Anda.';
        header("Location: detail_product.php?id_produk=" . $id_produk);
        exit();
    }
}
// Fungsi untuk menambahkan produk ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = $_POST['quantity'];

    // Mendapatkan stok terbaru dari database
    $query = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
    $query->bind_param("i", $id_produk);
    $query->execute();
    $result = $query->get_result();
    $stok_tersedia = $result->fetch_assoc()['stok'];

    // Periksa apakah total quantity di keranjang melebihi stok yang tersedia
    $quantity_in_cart = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if ($item['id_produk'] == $id_produk) {
                $quantity_in_cart += $item['quantity'];
                break;
            }
        }
    }

    $total_quantity = $quantity_in_cart + $quantity;

    // Jika total quantity melebihi stok yang tersedia
    if ($total_quantity > $stok_tersedia) {
        $_SESSION['message'] = 'Stok produk tidak masuk ke keranjang. Stok tersedia hanya ' . $stok_tersedia . ' unit.';
    } else {
        // Menambahkan item ke keranjang
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id_produk'] == $id_produk) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'id_produk' => $id_produk,
                'quantity' => $quantity,
                'harga' => $produk['harga'],
                'gambar' => $produk['gambar'],
                'nama_produk' => $produk['nama_produk']
            ];
        }

        $_SESSION['cart_message'] = 'Produk berhasil ditambahkan ke keranjang!';
    }

    // Redirect kembali ke halaman detail produk
    header("Location: detail_product.php?id_produk=" . $id_produk);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['cart_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['cart_message']; unset($_SESSION['cart_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produk['nama_produk']); ?> - Detail Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50; /* Green */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            opacity: 0;
            transition: opacity 0.5s;
        }

        .modal-content {
            max-width: 500px;
            margin: auto;
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <div class="container mt-3">
        <button onclick="history.back()" class="btn btn-secondary mb-3">Kembali</button>
    </div>

    <!-- Product Detail -->
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="../uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6">
                <h2 class="mt-3"><?php echo htmlspecialchars($produk['nama_produk']); ?></h2>
                <p class="fs-4 text-primary">Harga: Rp<?php echo number_format($produk['harga'], 2, ',', '.'); ?></p>
                <p class="text-muted">Stok Tersisa: <?php echo htmlspecialchars($produk['stok']); ?></p>
                <p><strong>Deskripsi:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>

                <form method="post" action="">
                    <input type="hidden" name="id_produk" value="<?php echo $produk['id_produk']; ?>">
                    <input type="hidden" name="id_user" value="<?php echo $id_user; ?>">

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah yang dimasukkan ke keranjang:</label>
                        <input type="number" class="form-control" name="quantity" id="quantity" value="1" min="1" max="<?php echo $produk['stok']; ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="add_to_cart" id="addToCartButton" class="btn btn-success" <?php echo $produk['stok'] <= 0 ? 'disabled' : ''; ?>>Tambah ke Keranjang</button>
                        <button type="button" id="orderNowButton" class="btn btn-primary" <?php echo $produk['stok'] == 0 ? 'disabled' : ''; ?>>Pesan Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <!-- Modal Order -->
    <div id="orderModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Metode Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm" method="post" action="">
                        <input type="hidden" name="id_produk" value="<?php echo htmlspecialchars($produk['id_produk']); ?>">
                        <input type="hidden" name="quantity" id="modalQuantity" value="1">

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Metode Pembayaran:</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="cod">Cash On Delivery</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Pengiriman:</label>
                            <textarea name="address" id="address" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="modalQuantityInput" class="form-label">Jumlah yang diinginkan:</label>
                            <input type="number" name="quantity" id="modalQuantityInput" class="form-control" value="1" min="1" max="<?php echo $produk['stok']; ?>" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="order_now" class="btn btn-success">Konfirmasi Pesanan</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
        // Notification
        function showNotification() {
            var notification = document.getElementById('notification');
            notification.style.display = 'block';
            setTimeout(function() {
                notification.style.opacity = '1';
            }, 100);

            setTimeout(function() {
                notification.style.opacity = '0';
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 500);
            }, 2000);
        }

        // Modal Order
        document.getElementById("orderNowButton").addEventListener("click", function () {
            var orderModal = new bootstrap.Modal(document.getElementById("orderModal"));
            orderModal.show();
        });
    </script>
</body>
</html>

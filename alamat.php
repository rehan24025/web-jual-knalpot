<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alamat dan Metode Pembayaran</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Masukkan Alamat dan Pilih Metode Pembayaran</h2>
        <form action="place_order.php" method="POST">
            <div class="mb-3">
                <label for="address" class="form-label">Alamat Pengiriman</label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="ovo">OVO</option>
                    <option value="gopay">GoPay</option>
                    <option value="bank_transfer">Transfer Bank</option>
                    <!-- Add other payment methods as needed -->
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Konfirmasi Pembelian</button>
        </form>
    </div>
</body>
</html>

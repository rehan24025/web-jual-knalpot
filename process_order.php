<?php
session_start();
include '../includes/db.php';

$response = ['success' => false, 'message' => 'Unknown error'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Anda harus login untuk melakukan pemesanan.';
    echo json_encode($response);
    exit;
}

$id_user = $_SESSION['user_id'];
$id_produk = $_POST['id_produk'];
$quantity = (int)$_POST['quantity'];
$payment_method = $_POST['payment_method'];
$address = $_POST['address'];

// Check stock
$query = $conn->prepare("SELECT stok, harga FROM produk WHERE id_produk = ?");
$query->bind_param("i", $id_produk);
$query->execute();
$result = $query->get_result();
$produk = $result->fetch_assoc();

if (!$produk || $produk['stok'] < $quantity) {
    $response['message'] = 'Stok tidak mencukupi untuk pesanan Anda.';
    echo json_encode($response);
    exit;
}

// Calculate total price
$total_price = $produk['harga'] * $quantity;

// Insert into orders
$kode_transaksi = "TRX-" . date('Ymd') . "-" . uniqid();
$insert_order = $conn->prepare("INSERT INTO orders (id_user, total_price, status_order, kode_transaksi) VALUES (?, ?, 'pending', ?)");
$insert_order->bind_param("iis", $id_user, $total_price, $kode_transaksi);

if ($insert_order->execute()) {
    $id_order = $insert_order->insert_id;

    // Insert into order_items
    $insert_item = $conn->prepare("INSERT INTO order_items (id_order, id_produk, quantity) VALUES (?, ?, ?)");
    $insert_item->bind_param("iii", $id_order, $id_produk, $quantity);
    
    if ($insert_item->execute()) {
        error_log("Insert ke order_items berhasil untuk quantity: " . $quantity);
    } else {
        error_log("Gagal insert ke order_items: " . $insert_item->error);
    }

    // Update stock
    $new_stock = $produk['stok'] - $quantity;
    $update_stok = $conn->prepare("UPDATE produk SET stok = ? WHERE id_produk = ?");
    $update_stok->bind_param("ii", $new_stock, $id_produk);
    
    if ($update_stok->execute()) {
        error_log("Stok berhasil diperbarui. Stok baru: " . $new_stock);
    } else {
        error_log("Gagal memperbarui stok: " . $update_stok->error);
    }

    $response['success'] = true;
    $response['message'] = 'Pesanan berhasil diproses!';
} else {
    $response['message'] = 'Terjadi kesalahan saat memproses pesanan.';
}

echo json_encode($response);
exit;
?>

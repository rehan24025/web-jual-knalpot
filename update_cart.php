<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$id_produk = $_POST['id_produk'];
$quantity = $_POST['quantity'];

// Cek ketersediaan stok produk
$query = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
$query->bind_param("i", $id_produk);
$query->execute();
$result = $query->get_result();
$product = $result->fetch_assoc();

if ($quantity <= $product['stok']) {
    // Update jumlah di keranjang
    $update_cart = $conn->prepare("UPDATE keranjang1 SET quantity = ? WHERE id_user = ? AND id_produk = ?");
    $update_cart->bind_param("iii", $quantity, $id_user, $id_produk);
    $update_cart->execute();
    header("Location: cart.php");
    exit();
} else {
    echo "<script>alert('Jumlah melebihi stok tersedia!'); window.location.href = 'cart.php';</script>";
}
?>

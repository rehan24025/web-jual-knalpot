<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$id_produk = $_POST['id_produk'];

// Hapus item dari keranjang
$delete_cart = $conn->prepare("DELETE FROM keranjang1 WHERE id_user = ? AND id_produk = ?");
$delete_cart->bind_param("ii", $id_user, $id_produk);
$delete_cart->execute();

header("Location: cart.php");
exit();
?>

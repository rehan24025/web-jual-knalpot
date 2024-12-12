<?php
session_start();
include '../includes/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan id_order ada dalam request GET
if (!isset($_GET['id_order'])) {
    echo "ID order tidak ditemukan.";
    exit();
}

$id_order = intval($_GET['id_order']);

// Hapus data terkait di order_items terlebih dahulu
$queryDeleteItems = "DELETE FROM order_items WHERE id_order = ?";
$stmtDeleteItems = $conn->prepare($queryDeleteItems);
$stmtDeleteItems->bind_param("i", $id_order);

if ($stmtDeleteItems->execute()) {
    // Jika penghapusan di order_items berhasil, lanjutkan ke orders
    $queryDeleteOrder = "DELETE FROM orders WHERE id_order = ?";
    $stmtDeleteOrder = $conn->prepare($queryDeleteOrder);
    $stmtDeleteOrder->bind_param("i", $id_order);
    
    if ($stmtDeleteOrder->execute()) {
        // Penghapusan berhasil, redirect ke halaman daftar order
        header("Location: manage_orders.php");
        exit();
    } else {
        echo "Gagal menghapus order: " . $stmtDeleteOrder->error;
    }
    $stmtDeleteOrder->close();
} else {
    echo "Gagal menghapus items terkait: " . $stmtDeleteItems->error;
}

$stmtDeleteItems->close();
$conn->close();
?>

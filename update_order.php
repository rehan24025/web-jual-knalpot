<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_order = $_POST['id_order'];
    $status_order = $_POST['status_order'];

    // Query untuk mengupdate status order
    $query = "UPDATE orders SET status_order = ? WHERE id_order = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status_order, $id_order);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Status order berhasil diperbarui.";
    } else {
        $_SESSION['message'] = "Gagal memperbarui status order.";
    }
    $stmt->close();
    $conn->close();

    // Redirect kembali ke halaman manage_orders.php
    header("Location: manage_orders.php");
    exit();
}
?>

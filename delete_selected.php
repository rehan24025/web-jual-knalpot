<?php
session_start();

if (isset($_POST['selected_items']) && !empty($_POST['selected_items'])) {
    $selected_items = $_POST['selected_items'];

    // Hapus item yang dipilih dari keranjang
    foreach ($selected_items as $key) {
        if (isset($_SESSION['cart'][$key])) {
            unset($_SESSION['cart'][$key]);
        }
    }

    $_SESSION['message'] = 'produk berhasi di hapus';
} else {
    $_SESSION['message'] = 'Tidak ada produk yang dipilih untuk dihapus.';
}

// Redirect kembali ke halaman keranjang setelah penghapusan
header('Location: cart.php');
exit();
?>

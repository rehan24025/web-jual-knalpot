<?php
session_start();
include_once '../includes/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_key = $_POST['product_key']; // Get the selected product key from the modal
    $new_quantity = $_POST['quantity'];   // Get the updated quantity from the form

    if (isset($_SESSION['cart'][$product_key])) {
        $selected_item = $_SESSION['cart'][$product_key];
        $product_id = $selected_item['id_produk'];

        // Validate the new quantity
        if ($new_quantity < 1) {
            $_SESSION['message'] = 'Jumlah produk tidak dapat kurang dari 1.';
            header('Location: cart.php');
            exit();
        }

        // Check if the new quantity exceeds the stock
        $query = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
        $query->bind_param("i", $product_id);
        $query->execute();
        $result = $query->get_result();
        $stok_db = $result->fetch_assoc()['stok'];

        if ($new_quantity > $stok_db) {
            $_SESSION['message'] = 'Stok tidak mencukupi untuk produk tersebut.';
            header('Location: cart.php');
            exit();
        }

        // Update the quantity in the session cart
        $_SESSION['cart'][$product_key]['quantity'] = $new_quantity;

        // Optionally, update the stock in the database (if you want to adjust the available stock)
        // $update_stock = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
        // $update_stock->bind_param("ii", $new_quantity, $product_id);
        // $update_stock->execute();

        // Redirect back to the cart
        header('Location: cart.php');
        exit();
    } else {
        $_SESSION['message'] = 'Item tidak ditemukan di dalam keranjang.';
        header('Location: cart.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'Tidak ada data yang dikirim.';
    header('Location: cart.php');
    exit();
}
?>

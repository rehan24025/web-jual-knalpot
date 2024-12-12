<?php
session_start();
include("../includes/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan ada item yang dipilih
    if (isset($_POST['selected_items'])) {
        $selected_items = $_POST['selected_items'];
        $payment_method = $_POST['payment_method'];
        $address = $_POST['address'];

        // Generate transaction code
        $kode_transaksi = 'TRX' . time();

        // Hitung total harga untuk item yang dicentang
        $total_price = 0;
        foreach ($selected_items as $item_id) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['id_produk'] == $item_id) {
                    $total_price += $item['harga'] * $item['quantity'];
                    break;
                }
            }
        }

        // Insert into orders table
        $query = "INSERT INTO orders (kode_transaksi, id_user, total_price, status_order, order_date, bukti_pembayaran, payment_method, address)
                  VALUES ('$kode_transaksi', {$_SESSION['user_id']}, $total_price, 'pending', NOW(), NULL, '$payment_method', '$address')";
        if (mysqli_query($conn, $query)) {
            $order_id = mysqli_insert_id($conn);

            // Insert into order_items table for selected products only
            foreach ($selected_items as $item_id) {
                foreach ($_SESSION['cart'] as $key => $item) {
                    if ($item['id_produk'] == $item_id) {
                        $order_item_query = "INSERT INTO order_items (id_order, id_produk, quantity, price)
                                             VALUES ($order_id, {$item['id_produk']}, {$item['quantity']}, {$item['harga']})";
                        mysqli_query($conn, $order_item_query);
                        break;
                    }
                }
            }

            // Clear the cart after the order is placed
            foreach ($_SESSION['cart'] as $key => $item) {
                // Hapus item yang sudah diproses
                if (in_array($item['id_produk'], $selected_items)) {
                    unset($_SESSION['cart'][$key]);
                }
            }

            // Redirect to order history or success page
            header("Location: order_success.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Pilih produk yang ingin dibeli.";
    }
}
?>

<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_produk = $_GET['id_produk'];
$query = $conn->prepare("SELECT * FROM produk WHERE id_produk = ?");
$query->bind_param("i", $id_produk);
$query->execute();
$result = $query->get_result();
$produk = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['user_id'];
    $quantity = $_POST['quantity'];
    $total_price = $produk['harga'] * $quantity;

    // Mendapatkan stok produk dari database
    $query = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
    $query->bind_param("i", $id_produk);
    $query->execute();
    $result = $query->get_result();
    $current_stock = $result->fetch_assoc()['stok'];

    // Fungsi untuk menambahkan produk ke keranjang dengan pengecekan stok
    function addToCart($id_produk, $quantity, $harga, $nama_produk, $gambar, $stok_tersedia) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $current_quantity_in_cart = 0;

        // Cek apakah produk sudah ada di keranjang dan hitung total quantity di keranjang
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id_produk'] == $id_produk) {
                $current_quantity_in_cart = $item['quantity'];
                break;
            }
        }

        // Hitung total quantity jika produk ditambahkan
        $total_quantity = $current_quantity_in_cart + $quantity;

        // Jika total quantity melebihi stok yang tersedia, batasi jumlah yang ditambahkan
        if ($total_quantity > $stok_tersedia) {
            $_SESSION['message'] = 'Maaf, stok kami terbatas. Stok tersedia: ' . $stok_tersedia;
            header("Location: detail_product.php?id_produk=" . $id_produk . "&error=out_of_stock");
            exit();
        }

        // Update atau tambahkan produk ke keranjang
        if ($current_quantity_in_cart > 0) {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id_produk'] == $id_produk) {
                    $item['quantity'] = $total_quantity;
                    return;
                }
            }
        } else {
            $_SESSION['cart'][] = [
                'id_produk' => $id_produk,
                'quantity' => $quantity,
                'harga' => $harga,
                'nama_produk' => $nama_produk,
                'gambar' => $gambar
            ];
        }
    }
}

// Tampilkan pesan peringatan jika ada
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']);
}
?>

<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_tipe = $_POST['nama_tipe'];

    $sql_tambah_tipe = "INSERT INTO tipe (nama_tipe) VALUES (?)";
    $stmt = $conn->prepare($sql_tambah_tipe);
    $stmt->bind_param("s", $nama_tipe);

    if ($stmt->execute()) {
        header("Location: manage_tipe.php?message=Tipe berhasil ditambahkan!");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tipe Produk</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<style>
    /* admin.css */

/* Atur margin dan padding agar lebih rapi */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

/* Header */
h2 {
    text-align: center;
    margin-top: 20px;
    color: #333;
}

/* Form Styling */
form {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

form input[type="text"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

form button {
    width: 100%;
    padding: 10px;
    border: none;
    background-color: #4CAF50;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

form button:hover {
    background-color: #45a049;
}

/* Pesan error atau konfirmasi */
.message {
    color: green;
    font-weight: bold;
    text-align: center;
    margin-top: 20px;
}

</style>
<body>

<h2>Tambah Tipe Produk Baru</h2>
<form method="POST">
    <input type="text" name="nama_tipe" placeholder="Nama Tipe Produk" required><br>
    <button type="submit">Tambah Tipe</button>
</form>

</body>
</html>

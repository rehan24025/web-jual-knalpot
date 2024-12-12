<?php
session_start();
include '../includes/db.php';

// Ambil data tipe produk untuk dropdown
$sql_tipe = "SELECT * FROM tipe";
$result_tipe = $conn->query($sql_tipe);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $id_tipe = $_POST['id_tipe'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = $_FILES['gambar']['name'];

    // Pindahkan gambar ke folder uploads
    move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/" . $gambar);

    $sql_insert = "INSERT INTO produk (nama_produk, harga, stok, id_tipe, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("sdisss", $nama_produk, $harga, $stok, $id_tipe, $deskripsi, $gambar);

    if ($stmt->execute()) {
        header("Location: manage_products.php?message=Produk berhasil ditambahkan!");
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
    <title>Tambah Produk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Tambah Produk</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Nama Produk -->
                    <div class="mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="nama_produk" name="nama_produk" placeholder="Masukkan nama produk" required>
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" placeholder="Masukkan harga produk" required>
                    </div>

                    <!-- Stok -->
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" placeholder="Masukkan jumlah stok" required>
                    </div>

                    <!-- Tipe Produk -->
                    <div class="mb-3">
                        <label for="id_tipe" class="form-label">Tipe Produk</label>
                        <select class="form-select" id="id_tipe" name="id_tipe" required>
                            <option value="">Pilih Tipe Produk</option>
                            <?php while ($tipe = $result_tipe->fetch_assoc()) { ?>
                                <option value="<?php echo $tipe['id_tipe']; ?>"><?php echo $tipe['nama_tipe']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Deskripsi Produk -->
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi produk" rows="4" required></textarea>
                    </div>

                    <!-- Gambar -->
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" required>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary w-100">Tambah Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

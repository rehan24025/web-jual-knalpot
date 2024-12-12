<?php
include '../includes/db.php';

$id_produk = $_GET['id'];
// Ambil data produk berdasarkan ID
$sql = "SELECT * FROM produk WHERE id_produk = $id_produk";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Ambil data tipe produk dari database
$sql_tipe = "SELECT * FROM tipe";
$result_tipe = $conn->query($sql_tipe);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $id_tipe = $_POST['id_tipe']; // ID tipe
    $stok = $_POST['stok']; // Tambahkan stok
    $gambar = $_FILES['gambar']['name'] ? $_FILES['gambar']['name'] : $row['gambar'];

    // Upload gambar baru jika ada
    if ($_FILES['gambar']['name']) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($gambar);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file);
    }

    // Update data produk
    $sql_update = "UPDATE produk 
                   SET nama_produk='$nama_produk', harga='$harga', deskripsi='$deskripsi', id_tipe='$id_tipe', stok='$stok', gambar='$gambar' 
                   WHERE id_produk=$id_produk";

    if ($conn->query($sql_update) === TRUE) {
        header('Location: manage_products.php?success=true');
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .success-message {
            display: none;
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h3 class="text-center mb-4">Edit Produk</h3>
        <div id="success-message" class="success-message">
            Edit berhasil! Anda akan dialihkan ke halaman produk...
        </div>
        <form action="edit_product.php?id=<?php echo $id_produk; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" id="nama_produk" name="nama_produk" class="form-control" value="<?php echo $row['nama_produk']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="text" id="harga" name="harga" class="form-control" value="<?php echo $row['harga']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" required><?php echo $row['deskripsi']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="id_tipe" class="form-label">Tipe Produk</label>
                <select id="id_tipe" name="id_tipe" class="form-select" required>
                    <option value="">Pilih Tipe Produk</option>
                    <?php while ($tipe = $result_tipe->fetch_assoc()) { ?>
                        <option value="<?php echo $tipe['id_tipe']; ?>" 
                            <?php echo ($row['id_tipe'] == $tipe['id_tipe']) ? 'selected' : ''; ?>>
                            <?php echo $tipe['nama_tipe']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" id="stok" name="stok" class="form-control" value="<?php echo $row['stok']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Produk</label>
                <input type="file" id="gambar" name="gambar" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary w-100">Edit Produk</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Cek jika edit berhasil
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        const successMessage = document.getElementById('success-message');
        successMessage.style.display = 'block';
        setTimeout(() => {
            window.location.href = 'manage_products.php';
        }, 1500);
    }
</script>

</body>
</html>

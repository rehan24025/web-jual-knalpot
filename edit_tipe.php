<?php
session_start();
include '../includes/db.php';

$id_tipe = $_GET['id'];

// Ambil data tipe berdasarkan ID
$sql = "SELECT * FROM tipe WHERE id_tipe = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_tipe);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_tipe = $_POST['nama_tipe'];

    // Update data tipe
    $sql_update = "UPDATE tipe SET nama_tipe = ? WHERE id_tipe = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $nama_tipe, $id_tipe);

    if ($stmt_update->execute()) {
        header("Location: manage_tipe.php?message=Tipe berhasil diupdate!");
        exit();
    } else {
        echo "Error: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tipe Produk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Tipe Produk</h2>

    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success text-center">
            <?php echo $_GET['message']; ?>
        </div>
    <?php endif; ?>

    <!-- Form untuk mengedit tipe produk -->
    <form method="POST">
        <div class="mb-3">
            <label for="nama_tipe" class="form-label">Nama Tipe</label>
            <input type="text" class="form-control" id="nama_tipe" name="nama_tipe" value="<?php echo htmlspecialchars($row['nama_tipe']); ?>" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Update Tipe</button>
    </form>

    <div class="mt-3 text-center">
        <a href="manage_tipe.php" class="btn btn-secondary">Kembali ke Daftar Tipe</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

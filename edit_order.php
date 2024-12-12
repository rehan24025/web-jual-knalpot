<?php
include '../includes/db.php';

$id_pesanan = $_GET['id'];
$sql = "SELECT * FROM pesanan WHERE id_pesanan = $id_pesanan";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $sql_update = "UPDATE pesanan SET status='$status' WHERE id_pesanan=$id_pesanan";
    if ($conn->query($sql_update) === TRUE) {
        header('Location: manage_orders.php');
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }
}
?>

<form action="edit_order.php?id=<?php echo $id_pesanan; ?>" method="POST">
    <label>Status Pesanan:</label>
    <select name="status">
        <option value="Diproses" <?php if ($row['status'] == 'Diproses') echo 'selected'; ?>>Diproses</option>
        <option value="Dikirim" <?php if ($row['status'] == 'Dikirim') echo 'selected'; ?>>Dikirim</option>
        <option value="Selesai" <?php if ($row['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
    </select>
    <button type="submit">Ubah Status</button>
</form>

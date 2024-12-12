<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Silakan login untuk melihat riwayat pesanan.";
    exit(); 
}

$id_user = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the delete button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all_orders'])) {
    $deleteOrderItemsQuery = "DELETE FROM order_items WHERE id_order IN (SELECT id_order FROM orders WHERE id_user = ?)";
    $deleteOrderItemsStmt = $conn->prepare($deleteOrderItemsQuery);
    $deleteOrderItemsStmt->bind_param("i", $id_user);
    $deleteOrderItemsStmt->execute();
    $deleteOrderItemsStmt->close();

    $deleteQuery = "DELETE FROM orders WHERE id_user = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $id_user);
    $deleteStmt->execute();
    $deleteStmt->close();

    header("Location: order_history.php");
    exit();
}

$query = "
    SELECT o.id_order, o.kode_transaksi, o.total_price, o.status_order, o.order_date, 
           GROUP_CONCAT(p.nama_produk SEPARATOR ', ') AS nama_produk,
           GROUP_CONCAT(oi.quantity SEPARATOR ', ') AS quantities
    FROM orders o
    JOIN order_items oi ON o.id_order = oi.id_order
    JOIN produk p ON oi.id_produk = p.id_produk
    WHERE o.id_user = ?
    GROUP BY o.id_order
    ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Riwayat Pesanan</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">ID Order: <?= htmlspecialchars($order['id_order']) ?></h5>
                                <h5 class="card-title">Kode Transaksi: <?= htmlspecialchars($order['kode_transaksi']) ?></h5>
                                <p class="card-text"><strong>Produk:</strong> <?= htmlspecialchars($order['nama_produk']) ?></p>
                                <p class="card-text"><strong>Jumlah:</strong> <?= htmlspecialchars($order['quantities']) ?></p>
                                <p class="card-text"><strong>Total Harga:</strong> Rp <?= number_format($order['total_price'], 2, ',', '.') ?></p>
                                <p class="card-text"><strong>Status:</strong> 
                                    <span class="badge bg-<?= $order['status_order'] === 'selesai' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($order['status_order']) ?>
                                    </span>
                                </p>
                                <p class="card-text"><small class="text-muted">Tanggal Pesan: <?= htmlspecialchars($order['order_date']) ?></small></p>
                                <?php if ($order['status_order'] === 'selesai'): ?>
                                    <button class="btn btn-primary btn-sm" onclick="showInvoice(<?= htmlspecialchars($order['id_order']) ?>)">Lihat Nota</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Tidak ada riwayat pesanan.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Fixed footer buttons -->
    <div class="fixed-bottom bg-light py-3 shadow">
        <div class="container d-flex justify-content-between">
            <a href="../product.php" class="btn btn-secondary">Kembali ke Produk</a>
            <form method="post" class="d-inline">
                <button name="delete_all_orders" class="btn btn-danger">Hapus Semua Riwayat Pesanan</button>
            </form>
        </div>
    </div>

    <!-- Modal for invoice -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nota Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="invoice-details">
                    <!-- Details will be injected here via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="printInvoice()">Cetak Nota</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showInvoice(orderId) {
            const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
            modal.show();

            fetch(`get_invoice.php?id_order=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    const invoiceDetails = document.getElementById("invoice-details");
                    invoiceDetails.innerHTML = `
                        <p><strong>ID Order:</strong> ${data.id_order}</p>
                        <p><strong>Produk:</strong> ${data.nama_produk}</p>
                        <p><strong>Jumlah:</strong> ${data.quantities}</p>
                        <p><strong>Total Harga:</strong> Rp ${parseFloat(data.total_price).toLocaleString('id-ID')}</p>
                        <p><strong>Status:</strong> ${data.status_order}</p>
                        <p><strong>Tanggal Pesan:</strong> ${data.order_date}</p>
                    `;
                });
        }

        function printInvoice() {
            const printContent = document.getElementById("invoice-details").innerHTML;
            const printWindow = window.open('', '', 'height=500, width=800');
            printWindow.document.write('<html><head><title>Nota Pesanan</title></head><body>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>

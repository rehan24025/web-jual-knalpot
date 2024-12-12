<?php
include '../includes/db.php';

if (!isset($_GET['id_order'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID Order tidak ditemukan']);
    exit();
}

$id_order = intval($_GET['id_order']);

$query = "
    SELECT o.id_order, o.total_price, o.status_order, o.order_date, 
           GROUP_CONCAT(p.nama_produk SEPARATOR ', ') AS nama_produk,
           GROUP_CONCAT(oi.quantity SEPARATOR ', ') AS quantities
    FROM orders o
    JOIN order_items oi ON o.id_order = oi.id_order
    JOIN produk p ON oi.id_produk = p.id_produk
    WHERE o.id_order = ?
    GROUP BY o.id_order
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_order);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Data tidak ditemukan']);
}
?>

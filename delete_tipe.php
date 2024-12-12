<?php
session_start();
include '../includes/db.php';  // Include your DB connection file

// Get the 'id' from the URL query parameter
$id_tipe = isset($_GET['id']) ? $_GET['id'] : null;

// Check if 'id' is provided
if ($id_tipe !== null) {
    // Step 1: Check if there are any products in the 'order_items' table using this 'id_tipe'
    $sql_check_related = "SELECT COUNT(*) FROM order_items oi
                          JOIN produk p ON oi.id_produk = p.id_produk
                          WHERE p.id_tipe = ?";
    $stmt_check = $conn->prepare($sql_check_related);
    $stmt_check->bind_param("i", $id_tipe);
    $stmt_check->execute();
    $stmt_check->bind_result($related_count);
    $stmt_check->fetch();
    $stmt_check->close();

    // Step 2: If there are related rows in 'order_items', prevent deletion
    if ($related_count > 0) {
        echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error:</strong> Tidak bisa menghapus tipe ini karena ada produk yang menggunakan tipe tersebut.
              </div>
              <a href='manage_tipe.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
        exit();
    }

    // Step 3: Prepare SQL query to delete from 'tipe' table
    $sql_delete = "DELETE FROM tipe WHERE id_tipe = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_tipe);

    // Step 4: Execute the query and check if successful
    if ($stmt_delete->execute()) {
        // Display success notification with return button
        echo "<div style='padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;'>
                <strong>Success:</strong> Tipe berhasil dihapus!
              </div>
              <a href='manage_tipe.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
        exit();
    } else {
        // Display error if deletion fails
        echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error:</strong> " . htmlspecialchars($stmt_delete->error, ENT_QUOTES, 'UTF-8') . "
              </div>
              <a href='manage_tipe.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
    }
    $stmt_delete->close();
} else {
    // If 'id' is not provided, show an error
    echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
            <strong>Error:</strong> ID tipe tidak ditemukan.
          </div>
          <a href='manage_tipe.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
}
?>

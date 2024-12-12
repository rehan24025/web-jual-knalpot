<?php
session_start();
include '../includes/db.php';  // Include your DB connection file

// Get the 'id' from the URL query parameter
$id_user = isset($_GET['id']) ? $_GET['id'] : null;
$current_user_id = $_SESSION['user_id'];  // Assuming 'user_id' is stored in the session after login

// Check if 'id' is provided
if ($id_user !== null) {
    // Step 1: Prevent the logged-in user from deleting themselves
    if ($id_user == $current_user_id) {
        echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error:</strong> Anda tidak bisa menghapus akun Anda sendiri.
              </div>
              <a href='manage_users.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
        exit();
    }

    // Step 2: Check if the user has any related records in the orders table
    $sql_check_related = "SELECT COUNT(*) FROM orders WHERE id_user = ?";
    $stmt_check = $conn->prepare($sql_check_related);
    $stmt_check->bind_param("i", $id_user);
    $stmt_check->execute();
    $stmt_check->bind_result($related_count);
    $stmt_check->fetch();
    $stmt_check->close();

    // Step 3: If there are related records, prevent deletion
    if ($related_count > 0) {
        echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error:</strong> Tidak bisa menghapus pengguna ini karena ada transaksi yang terkait dengan pengguna tersebut.
              </div>
              <a href='manage_users.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
        exit();
    }

    // Step 4: Prepare SQL query to delete from 'users' table
    $sql_delete = "DELETE FROM users WHERE id_user = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_user);

    // Step 5: Execute the query and check if successful
    if ($stmt_delete->execute()) {
        // Display success notification with return button
        echo "<div style='padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;'>
                <strong>Success:</strong> Pengguna berhasil dihapus!
              </div>
              <a href='manage_users.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
        exit();
    } else {
        // Display error if deletion fails
        echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error:</strong> " . htmlspecialchars($stmt_delete->error, ENT_QUOTES, 'UTF-8') . "
              </div>
              <a href='manage_users.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
    }
    $stmt_delete->close();
} else {
    // If 'id' is not provided, show an error
    echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
            <strong>Error:</strong> ID pengguna tidak ditemukan.
          </div>
          <a href='manage_users.php' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali</a>";
}
?>

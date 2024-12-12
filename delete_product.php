<?php
// Include your database connection
include('../includes/db.php');

// Check if the 'id_produk' is passed in the URL
if (isset($_GET['id_produk'])) {
    $id_produk = $_GET['id_produk'];

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // First, delete the related rows from the 'order_items' table
        $delete_order_items = "DELETE FROM order_items WHERE id_produk = ?";
        $stmt = $conn->prepare($delete_order_items);
        $stmt->bind_param("i", $id_produk);
        $stmt->execute();

        // Then, delete the product from the 'produk' table
        $delete_product = "DELETE FROM produk WHERE id_produk = ?";
        $stmt = $conn->prepare($delete_product);
        $stmt->bind_param("i", $id_produk);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect to the product list or another page with a success message
        header("Location: manage_products.php?message=Product deleted successfully");
        exit;
    } catch (Exception $e) {
        // If any error occurs, rollback the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no 'id_produk' is passed, show an error message
    echo "Error: Product ID is missing.";
}

?>

<?php
include '../includes/db.php';

// Query untuk menghitung total produk, total pesanan, dan total pengguna
$sqlTotalProducts = "SELECT COUNT(*) AS total_products FROM produk";
$totalProductsResult = $conn->query($sqlTotalProducts);
$totalProducts = $totalProductsResult->fetch_assoc()['total_products'];

$sqlTotalOrders = "SELECT COUNT(*) AS total_orders FROM orders";
$totalOrdersResult = $conn->query($sqlTotalOrders);
$totalOrders = $totalOrdersResult->fetch_assoc()['total_orders'];

$sqlTotalUsers = "SELECT COUNT(*) AS total_users FROM users";
$totalUsersResult = $conn->query($sqlTotalUsers);
$totalUsers = $totalUsersResult->fetch_assoc()['total_users'];

// Query untuk data penjualan per bulan
$sqlSalesPerMonth = "SELECT MONTH(order_date) AS month, COUNT(id_order) AS total_sales 
                     FROM orders 
                     GROUP BY MONTH(order_date)";
$salesPerMonthResult = $conn->query($sqlSalesPerMonth);

// Inisialisasi $salesData dengan nilai 0 untuk setiap bulan
$salesData = array_fill(0, 12, 0);

while ($row = $salesPerMonthResult->fetch_assoc()) {
    // Isi total penjualan untuk bulan yang tersedia
    $salesData[intval($row['month']) - 1] = $row['total_sales'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Knalpot Racing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .admin-panel .card {
            text-align: center;
        }
        .chart-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <h2 class="text-center">Admin Menu</h2>
        <a href="manage_products.php" class="btn btn-dark">Manage Products</a>
        <a href="manage_users.php" class="btn btn-dark">Manage Users</a>
        <a href="manage_tipe.php" class="btn btn-dark">Manage Types</a>
        <a href="manage_orders.php" class="btn btn-dark">Manage Orders</a>
        <a href="../user/logout.php" class="btn btn-dark">Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="mb-4">Admin Dashboard</h1>

        <!-- Overview Panel -->
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3>Total Products</h3>
                        <p class="fs-4"><?php echo $totalProducts; ?> Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3>Total Orders</h3>
                        <p class="fs-4"><?php echo $totalOrders; ?> Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3>Total Users</h3>
                        <p class="fs-4"><?php echo $totalUsers; ?> Users</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="chart-container mt-5">
            <h3>Sales Chart</h3>
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Chart.js Initialization -->
    <script>
        const salesData = <?php echo json_encode(array_values($salesData)); ?>;
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'Monthly Orders',
                    data: salesData,
                    backgroundColor: 'rgba(29, 114, 184, 0.2)',
                    borderColor: 'rgba(29, 114, 184, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

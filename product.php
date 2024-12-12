<?php
session_start();
include 'includes/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

// Ambil tipe produk untuk dropdown filter
$sql_tipe = "SELECT * FROM tipe";
$result_tipe = $conn->query($sql_tipe);

// Tangkap parameter search dan filter tipe
$search = isset($_GET['search']) ? $_GET['search'] : '';
$id_tipe = isset($_GET['id_tipe']) ? $_GET['id_tipe'] : '';

// Buat query produk berdasarkan search dan filter tipe
$sql = "SELECT * FROM produk WHERE 1=1";
if ($search) {
    $sql .= " AND nama_produk LIKE ?";
}
if ($id_tipe) {
    $sql .= " AND id_tipe = ?";
}

$stmt = $conn->prepare($sql);

// Bind parameter secara dinamis
if ($search && $id_tipe) {
    $stmt->bind_param("si", $search_param, $id_tipe);
    $search_param = "%$search%";
} elseif ($search) {
    $stmt->bind_param("s", $search_param);
    $search_param = "%$search%";
} elseif ($id_tipe) {
    $stmt->bind_param("i", $id_tipe);
}

$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/shop.css">
    <style>
        /* Custom styles for cards */
        .card {
            width: 16rem; /* Adjust card width */
            margin: 0 auto; /* Center the card in its column */
        }

        .card-img-top {
            max-height: 150px; /* Limit image height */
            object-fit: cover; /* Ensure the image scales properly */
        }

        .card-body {
            font-size: 0.9rem; /* Reduce font size slightly */
        }

        /* Adjust padding for smaller screens */
        @media (max-width: 576px) {
            .card {
                width: 100%; /* Take full width on small screens */
            }
        }
        /* Sidebar */
#mySidebar {
    z-index: 1050; /* Prioritas sidebar */
}

/* Overlay */
#overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Efek gelap */
    z-index: 1040; /* Overlay di bawah sidebar */
    display: none; /* Sembunyikan secara default */
    pointer-events: none; /* Default: tidak menerima klik */
}

#overlay.active {
    display: block;
    pointer-events: auto; /* Aktifkan interaksi */
}

/* Nonaktifkan scroll saat sidebar terbuka */
.no-scroll {
    overflow: hidden;
}

    </style>
</head>
<body>
<header class="bg-light border-bottom mb-3">
    <div class="container d-flex justify-content-between align-items-center py-3">
        <!-- Logo -->
        <div class="d-flex align-items-center">
            <img src="uploads/logokiri.png" alt="Logo Knolpot Racing Store" class="me-3" style="height: 50px;">
            <h1 class="h4 mb-0">Knolpot Racing Store</h1>
        </div>
        <!-- Header Icons -->
        <div class="header-icons">
            <a href="user/cart.php" class="btn btn-outline-primary me-2 btn-sm">🛒 Cart</a>
            <button class="btn btn-outline-secondary btn-sm" onclick="openSidebar()">⚙️ Settings</button>
        </div>
    </div>
</header>

<!-- Sidebar -->
<div id="mySidebar" class="bg-light border-end position-fixed top-0 start-0 vh-100" style="width: 0; overflow-x: hidden; transition: 0.5s;">
    <a href="javascript:void(0)" class="btn-close position-absolute top-0 end-0 mt-2 me-2" onclick="closeSidebar()"></a>
    <ul class="list-unstyled mt-4 ps-3">
        <li><a href="user/profile.php" class="text-decoration-none d-block py-2">Profile</a></li>
        <li><a href="index.php" class="text-decoration-none d-block py-2">Home</a></li>
        <li><a href="user/logout.php" class="text-decoration-none d-block py-2">Logout</a></li>
        <li><a href="user/order_history.php" class="text-decoration-none d-block py-2">Riwayat Pesanan</a></li>
    </ul>
</div>

<div id="overlay" onclick="closeSidebar()"></div>


<!-- Overlay -->
<div id="overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="display: none;" onclick="closeSidebar()"></div>

<!-- Search Bar -->
<div class="container mb-4">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search product..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="id_tipe" class="form-select">
                <option value="">All Types</option>
                <?php while ($tipe = $result_tipe->fetch_assoc()) { ?>
                    <option value="<?php echo $tipe['id_tipe']; ?>" <?php echo ($id_tipe == $tipe['id_tipe']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tipe['nama_tipe']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>
</div>

<!-- Product Grid -->
<div class="container">
    <div class="row row-cols-1 row-cols-md-4 g-3"> <!-- Max 4 per row with gaps -->
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['nama_produk']); ?></h5>
                            <p class="card-text">Price: Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                            <a href="user/detail_product.php?id_produk=<?php echo htmlspecialchars($row['id_produk']); ?>" 
                               class="btn btn-primary btn-sm">Lihat!</a>
                        </div>
                    </div>
                </div>
            <?php }
        } else {
            echo "<p>No products found.</p>";
        } ?>
    </div>
</div>



<?php include 'includes/footer.php'; ?>

<script>
    function openSidebar() {
    document.getElementById("mySidebar").style.width = "250px"; // Buka sidebar
    const overlay = document.getElementById("overlay");
    overlay.classList.add("active"); // Tampilkan overlay
    document.body.classList.add("no-scroll"); // Nonaktifkan scroll
}

function closeSidebar() {
    document.getElementById("mySidebar").style.width = "0"; // Tutup sidebar
    const overlay = document.getElementById("overlay");
    overlay.classList.remove("active"); // Sembunyikan overlay
    document.body.classList.remove("no-scroll"); // Aktifkan kembali scroll
}


</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php
session_start();
include 'includes/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

// Ambil data pengguna dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT profile_pic FROM users WHERE id_user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_image);
$stmt->fetch();
$stmt->close();

// Tentukan URL gambar profil (gunakan gambar default jika tidak ada)
$profile_image_url = $profile_image ? "uploads/$profile_image" : "uploads/default_profile.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Knalpot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css"> <!-- Custom CSS file -->
</head>
<body>
<header>
    <div class="header-container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="uploads/logokiri.png" alt="Logo" class="logo"> <!-- Pastikan file logo.png ada -->
            <h1 class="store-name">Knolpot Racing</h1> <!-- Nama toko -->
        </div>
        <div class="profile-container">
            <img src="<?= $profile_image_url ?>" alt="Foto Profil" class="profile-img rounded-circle" style="width: 50px; height: 50px;">
        </div>
    </div>
</header>

<!-- Carousel Section -->
<div id="carouselExampleIndicators" class="carousel slide shadow p-3 mb-5 bg-white rounded" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="uploads/logodepan1.png" class="d-block w-100" alt="Slide 1">
            <div class="carousel-caption d-none d-md-block">
                <h1 class="btn-grey">Welcome to Toko Knalpot</h1>
                <a href="product.php" class="btn btn-grey shadow-sm">Lihat Produk</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="uploads/logodepan2.png" class="d-block w-100" alt="Slide 2">
            <div class="carousel-caption d-none d-md-block">
                <a href="jelajahi.php" class="btn btn-primary">Jelajahi Sekarang</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="uploads/logodepan3.png" class="d-block w-100" alt="Slide 3">
            <div class="carousel-caption d-none d-md-block">
                <h1>Knalpot Menarik!</h1>
                <a href="product.php" class="btn btn-primary">Lihat produk boss</a>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- About Section -->
<section class="about-section">
    <div class="container text-center mt-4">
        <h2>Tentang Toko Knalpot</h2>
        <p>Toko Knalpot adalah toko terpercaya yang menyediakan berbagai jenis knalpot berkualitas tinggi. Kami menawarkan produk terbaik dengan harga bersaing untuk para pecinta otomotif. Temukan berbagai pilihan knalpot yang sesuai dengan kebutuhan kendaraan Anda.</p>
        <img src="uploads/logoo.png" alt="Toko Knalpot" class="about-img mt-3">
    </div>
</section>

<!-- Icon Boxes Section -->
<section class="icon-boxes mt-4">
    <div class="d-flex justify-content-center gap-4">
        <div class="icon-box text-center">
            <a href="user/order_history.php">
                <div class="icon-container">
                    <img src="uploads/pesan.png" alt="Home Icon">
                </div>
                <p>pesanan</p>
            </a>
        </div>
        <div class="icon-box text-center">
            <a href="user/cart.php">
                <div class="icon-container">
                    <img src="uploads/keranjang.png" alt="Cart Icon">
                </div>
                <p>Cart</p>
            </a>
        </div>
        <div class="icon-box text-center">
            <a href="product.php">
                <div class="icon-container">
                    <img src="uploads/iconker.png" alt="Product Icon">
                </div>
                <p>Produk</p>
            </a>
        </div>
    </div>
</section>

<!-- Footer Section -->
<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>

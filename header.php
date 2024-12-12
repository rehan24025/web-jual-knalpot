<div class="header-container">
    <!-- Logo -->
    <div class="logo">
        <a href="index.php">Knalpot Racing Store</a>
    </div>

    <!-- Navigasi -->
    <nav class="nav-links">
        <?php if (isset($_SESSION['user_id'])) { ?>
            
        <?php } else { ?>
            <a href="user/login.php">Login</a>
        <?php } ?>
    </nav>

    <!-- Keranjang dan Sidebar dalam satu div -->
    <div class="right-icons">
        <a href="user/cart.php" class="cart-icon">
            <img src="uploads/keranjang.png" alt="Cart" width="30" height="30">
        </a>
        <button class="open-btn" onclick="openSidebar()">☰</button>
    </div>
</div>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
    <a href="javascript:void(0)" class="close-btn" onclick="closeSidebar()">×</a>
    <a href="index.php">Home</a>
    <a href="product.php">Produk</a>
    <a href="user/cart.php">Cart</a>
    <a href="user/profile.php">Profil</a>
    <a href="user/logout.php">Logout</a>
</div>

<style>
    /* Gaya Header */
    .header-container {
        max-width: 1200px;
        margin: auto;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #333;
        color: white;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-radius: 15px;
    }

    .logo a {
        font-size: 24px;
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    nav a {
        color: white;
        margin: 0 10px;
        text-decoration: none;
        font-size: 18px;
    }

    nav a:hover {
        text-decoration: underline;
    }

    /* Styling untuk ikon keranjang dan tombol sidebar */
    .right-icons {
        display: flex;
        align-items: center;
        gap: 10px; /* Jarak antara ikon keranjang dan tombol sidebar */
    }

    .cart-icon img {
        vertical-align: middle;
    }

    .open-btn {
        font-size: 24px;
        background: none;
        border: none;
        color: white;
        cursor: pointer;
    }

    /* Sidebar */
    .sidebar {
        height: 100%;
        width: 0;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #111;
        overflow-x: hidden;
        transition: 0.5s;
        padding-top: 60px;
        z-index: 2000;
    }

    .sidebar a {
        padding: 15px;
        text-decoration: none;
        font-size: 18px;
        color: white;
        display: block;
        transition: 0.3s;
    }

    .sidebar a:hover {
        background-color: #575757;
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 25px;
        font-size: 36px;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        nav {
            display: none;
        }

        .open-btn {
            display: block;
        }
    }
</style>

<script>
    function openSidebar() {
        document.getElementById("mySidebar").style.width = "250px";
    }

    function closeSidebar() {
        document.getElementById("mySidebar").style.width = "0";
    }
</script>

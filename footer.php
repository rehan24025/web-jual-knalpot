<footer class="k">
    <div class="footer-container">
        <!-- Bagian Kiri: Sponsor -->
        <div class="footer-left">
            <h3>Sponsor Kami</h3>
            <div class="sponsor-logos">
                <img src="./uploads/logo.jpeg" alt="Sponsor 1" class="sponsor-logo">
                <img src="./uploads/sponsor2.png" alt="Sponsor 2" class="sponsor-logo">
                <img src="./uploads/sponsor3.png" alt="Sponsor 3" class="sponsor-logo">
                <img src="./uploads/tiktok.png" alt="Sponsor 3" class="sponsor-logo">
                <img src="./uploads/sponsor4.png" alt="Sponsor 3" class="sponsor-logo">
            </div>
        </div>

        <!-- Bagian Tengah: Keunggulan Website -->
        <div class="footer-center">
            <h3>Keunggulan Kami</h3>
            <ul>
                <li>    Produk asli dan berkualitas tinggi</li>
                <li>    Pengiriman cepat dan aman</li>
                <li>    Sistem pemesanan praktis dan responsif</li>
            </ul>
        </div>

        <!-- Bagian Kanan: Tentang Website -->
        <div class="footer-right">
            <h3>Tentang Kami</h3>
            <p>Knalpot Racing Store adalah platform terpercaya untuk semua kebutuhan knalpot Anda. Kami menyediakan berbagai jenis knalpot racing berkualitas untuk meningkatkan performa kendaraan Anda.</p>
        </div>
    </div>
    <p class="copyright">&copy; <?php echo date('Y'); ?> Knalpot Racing Store. All rights reserved.</p>
</footer>

<style>
    .k{
        background-color:#bfc4c9;
    }
    /* Gaya untuk footer */
    footer {
        background-color: #333;
        color: white;
        padding: 20px;
        /* position: fixed; */
        bottom: 0;
        width: 100%;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
    }

    .footer-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
        max-width: 1200px;
        margin: auto;
        background-color:#bfc4c9;
    }

    .footer-left, .footer-center, .footer-right {
        flex: 1;
        min-width: 250px;
    }

    h3 {
        margin-bottom: 10px;
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    ul li {
        margin-bottom: 8px;
    }

    .sponsor-logos {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .sponsor-logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }

    .copyright {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .footer-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    }
</style>

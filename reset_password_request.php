<?php
include '../includes/db.php';
$message = ''; // Inisialisasi pesan

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    // Cek apakah pengguna ada di database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Simpan permintaan reset password di database
        $stmt = $conn->prepare("INSERT INTO password_resets (username, status) VALUES (?, 'pending')");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        
        $message = "Permintaan reset password telah dikirim. Tunggu 3 menit dengan pw:123 jika gagal coba lagi.";
        $messageType = "success";
    } else {
        $message = "Username tidak ditemukan!";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
        }

        .container {
            max-width: 400px;
            margin-top: 50px;
        }

        h2 {
            text-align: center;
        }

        .notification {
            display: none;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-weight: bold;
            position: relative;
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .notification.success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .notification.error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .notification.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .notification.hide {
            opacity: 0;
            transform: translateY(-20px);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Permintaan Reset Password</h2>
    <form method="post" id="resetForm">
        <?php if (!empty($message)): ?>
            <div id="notification" class="notification <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="mb-3">
            <label for="username" class="form-label">Masukkan Username:</label>
            <input type="text" class="form-control" name="username" id="username" required>
        </div>
        
        <button type="submit" class="btn btn-success w-100">Kirim Permintaan</button>
    </form>
    
    <div class="mt-3 text-center">
        <a href="login.php" class="btn btn-link">Kembali ke Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // JavaScript untuk animasi notifikasi
    document.addEventListener("DOMContentLoaded", function() {
        const notification = document.getElementById("notification");

        if (notification) {
            notification.classList.add("show");

            // Hilangkan notifikasi setelah beberapa detik
            setTimeout(() => {
                notification.classList.add("hide");
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 500); // Waktu sinkron dengan animasi
            }, 3000); // Ubah durasi waktu tampil di sini
        }
    });
</script>

</body>
</html>

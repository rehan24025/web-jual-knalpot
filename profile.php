<?php
include '../includes/db.php';

// Periksa apakah pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mendapatkan data pengguna berdasarkan session user_id
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Memproses form saat disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
    
    // Proses upload gambar profil jika ada
    if (!empty($_FILES['profile_pic']['name'])) {
        $profile_pic = 'profile-' . $user_id . '-' . time() . '.' . pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], '../uploads/' . $profile_pic);
    } else {
        $profile_pic = $user['profile_pic']; // Gunakan gambar lama jika tidak diubah
    }

    // Update data pengguna di database
    $update_sql = "UPDATE users SET username = ?, password = ?, profile_pic = ? WHERE id_user = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $nama, $password, $profile_pic, $user_id);
    
    if ($update_stmt->execute()) {
        header("Location: profile.php?success=true");
        exit();
    } else {
        echo "<script>alert('Failed to update profile: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .success-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 18px;
            z-index: 9999;
        }
        .success-popup.active {
            display: block;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Profil Pengguna</h1>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <div class="text-center">
                    <img src="../uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="profile-pic">
                </div>
                <form action="profile.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Pengguna</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru (kosongkan jika tidak ingin mengganti)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="profile_pic" class="form-label">Gambar Profil</label>
                        <input type="file" name="profile_pic" id="profile_pic" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Perbarui Profil</button>
                </form>
            </div>

            <!-- Tombol Kembali ke Beranda -->
            <div class="mt-3 text-center">
                <a href="../index.php" class="btn btn-secondary">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>

<!-- Pop-up berhasil -->
<div class="success-popup" id="success-popup">
    Profil berhasil diperbarui!
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Display success pop-up if the URL contains success=true
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        const popup = document.getElementById('success-popup');
        popup.classList.add('active');
        setTimeout(() => {
            popup.classList.remove('active');
            window.location.href = '../index.php'; // Redirect after 3 seconds
        }, 3000); // Pop-up tetap selama 3 detik sebelum redirect
    }
</script>

</body>
</html>

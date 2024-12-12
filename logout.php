<?php
session_start();

// Hapus session
session_unset();
session_destroy();

// Hapus cookies
setcookie("user_id", "", time() - 3600, "/");
setcookie("username", "", time() - 3600, "/");
setcookie("role", "", time() - 3600, "/");

// Redirect ke halaman login
header("Location: login.php");
exit();
?>

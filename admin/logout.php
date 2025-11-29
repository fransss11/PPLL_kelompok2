<?php
session_start();

// Menghapus semua variabel session
session_unset();

// Menghancurkan session sepenuhnya
session_destroy();

// Redirect ke halaman login (naik satu folder sesuai permintaan)
header("Location: ../loginAdmin.php");
exit;
?>
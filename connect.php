<?php
    $servername = "localhost"; // Nama server database
    $username = "root"; // Username database
    $password = ""; // Password database
    $dbname = "wisata_ppll"; // Nama database

    // Membuat koneksi ke database MySQL
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error); // Tampilkan error jika koneksi gagal
    }
?>

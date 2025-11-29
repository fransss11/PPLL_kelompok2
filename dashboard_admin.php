<?php
session_start();
include "connect.php";

// Jika belum login -> redirect ke login
// if (!isset($_SESSION['admin'])) {
//     header("Location: loginAdmin.php");
//     exit();
// }

// $username = $_SESSION['username'];
$query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-image: url("images/services-1.jpg");
            background-size: cover;
            background-position: center;
            background-color: rgba(0,0,0,0.45);
            background-blend-mode: darken;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -230px;
            width: 230px;
            height: 100%;
            background: rgba(255,255,255,0.92);
            padding: 0px;
            box-shadow: 2px 0 8px rgba(0,0,0,0.3);
            border-radius: 0 10px 10px 0;
            transition: 0.3s ease;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar h2 {
            margin-top: 0;
            color: #0056b3;
            text-align: center;
        }

        .profile-name {
            font-weight: bold;
            color: #222;
            text-align: center;
        }

        .profile-level {
            font-size: 14px;
            text-align: center;
            color: #555;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            margin-top: 10px;
            text-decoration: none;
            color: #222;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background: #007bff;
            color: #fff;
        }

        .logout {
            margin-top: 20px;
            background: #c40000;
            color: white !important;
        }

        .logout:hover {
            background: #960000 !important;
        }

        /* Main Content */
        .main-content {
            padding: 25px;
            transition: 0.3s;
        }

        .main-content.shift {
            margin-left: 250px;
        }

        .toggle-btn {
            font-size: 30px;
            color: white;
            cursor: pointer;
            margin: 10px;
            text-shadow: 0 0 4px black;
        }

        .content-box {
  
            width: fit-content;
            padding: 30px 50px;
            border-radius: 12px;
            margin-top: 50px;
            box-shadow: 0 0 15px rgba(229, 204, 204, 0.3);
        }

        .content-box h1 {
            margin-top: 0;
        }
    </style>

    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
            document.querySelector(".main-content").classList.toggle("shift");
        }
    </script>

</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Menu Admin</h2>

    <!-- Menu navigasi -->
    <a href="dashboard_admin.php">Dashboard</a>
    <a href="data_admin.php">Data Admin</a>
    <a href="data_wisata.php">Data Wisata</a>

    <hr>

    <!-- Logout -->
    <a href="logout.php" class="logout">Logout</a>
</div>


<!-- Main Content -->
<div class="main-content">
    <span class="toggle-btn" onclick="toggleSidebar()">☰</span>

    <div class="content-box">
        <h1>Selamat Datang👋</h1>
        <p>Ini adalah halaman Dashboard Admin Sistem Informasi Wisata Bangkalan.</p>
    </div>
</div>

</body>
</html>

<?php
session_start();
include "connect.php";   // file koneksi database

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("Location: admin/dashboard_admin.php");
    exit();
}

// PROSES LOGIN
if (isset($_POST['login'])) {

    // Pakai real_escape_string biar aman dari hack dasar
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Ambil data admin berdasarkan username
    $query = "SELECT * FROM admin WHERE username='$username' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        // Cek Password (Pastikan di database passwordnya plain text/belum di-hash)
        if ($password == $data['password']) {
            
            // ===========================================
            // BAGIAN PENTING: SIMPAN DATA KE SESSION
            // ===========================================
            $_SESSION['id_admin']   = $data['id_admin'];
            $_SESSION['nama_admin'] = $data['nama_admin'];
            $_SESSION['username']   = $data['username'];
            $_SESSION['level']      = $data['level']; // <--- WAJIB ADA BIAR FITUR SUPERADMIN JALAN
            $_SESSION['status']     = "login";

            echo "<script>alert('Login berhasil! Selamat Datang " . $data['nama_admin'] . "'); window.location='admin/dashboard_admin.php';</script>";
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;

            /* 🔥 Background gambar */
            background-image: url('images/services-1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;

            /* overlay sedikit gelap biar lebih elegan */
            background-color: rgba(0, 0, 0, 0.4);
            background-blend-mode: darken;
        }

        .login-box {
            width: 320px;
            background: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            backdrop-filter: blur(4px);
        }

        .login-box h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"], input[type="password"] {
            width: 92%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #bbb;
            font-size: 15px;
        }

        button {
            background: #007bff;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #0056b3;
        }

        .error-box {
            background: #ffdddd;
            color: #a10000;
            padding: 10px;
            border-left: 5px solid red;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login Admin</h2>

    <?php if (!empty($error)) : ?>
        <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
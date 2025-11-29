<?php
session_start();
include "connect.php";   // file koneksi database

// Jika sudah login langsung ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: dashboard_admin.php");
    exit();
}

// PROSES LOGIN
if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data admin
    $query = "SELECT * FROM admin WHERE username='$username' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        if ($password == $data['password']) {
            echo "<script>alert('Login berhasil!'); window.location='dashboard_admin.php';</script>";
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

    <!-- error -->
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

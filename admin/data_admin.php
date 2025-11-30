<?php
session_start();
include "../connect.php"; // Mundur satu folder untuk ambil connect.php

// --- 1. KEAMANAN: CEK LOGIN ---
// Jika belum login, tendang ke loginAdmin.php di folder luar
if (empty($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../loginAdmin.php"); 
    exit();
}

// Ambil level dari session
$level_user = $_SESSION['level']; // isinya 'admin' atau 'superadmin'

// ==========================================
// BAGIAN LOGIC PHP (HANYA UNTUK SUPERADMIN)
// ==========================================

// Logika ini hanya akan jalan jika levelnya superadmin
if ($level_user == 'superadmin') {

    // 1. Tambah Admin
    if (isset($_POST['simpan_admin'])) {
        $nama     = mysqli_real_escape_string($conn, $_POST['nama_admin']);
        $user     = mysqli_real_escape_string($conn, $_POST['username']);
        $pass     = mysqli_real_escape_string($conn, $_POST['password']);
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $level    = $_POST['level'];

        // Cek username kembar
        $cek = mysqli_query($conn, "SELECT username FROM admin WHERE username='$user'");
        if (mysqli_num_rows($cek) > 0) {
            echo "<script>alert('Username sudah digunakan!');</script>";
        } else {
            $query = "INSERT INTO admin (nama_admin, username, password, email, level) 
                      VALUES ('$nama', '$user', '$pass', '$email', '$level')";
            
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Admin Berhasil Ditambah'); window.location='data_admin.php';</script>";
            } else {
                echo "<script>alert('Gagal menambah admin');</script>";
            }
        }
    }

    // 2. Update Admin
    if (isset($_POST['update_admin'])) {
        $id       = $_POST['id_admin'];
        $nama     = mysqli_real_escape_string($conn, $_POST['nama_admin']);
        $user     = mysqli_real_escape_string($conn, $_POST['username']);
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $level    = $_POST['level'];
        $pass_baru = $_POST['password'];

        // Jika password kosong, jangan diupdate
        if (empty($pass_baru)) {
            $query = "UPDATE admin SET nama_admin='$nama', username='$user', email='$email', level='$level' WHERE id_admin='$id'";
        } else {
            $pass_safe = mysqli_real_escape_string($conn, $pass_baru);
            $query = "UPDATE admin SET nama_admin='$nama', username='$user', password='$pass_safe', email='$email', level='$level' WHERE id_admin='$id'";
        }

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Data Admin Diupdate'); window.location='data_admin.php';</script>";
        }
    }

    // 3. Hapus Admin
    if (isset($_GET['hapus_admin'])) {
        $id = $_GET['hapus_admin'];
        // Proteksi agar tidak menghapus akun yang sedang login
        if($_SESSION['id_admin'] == $id){
             echo "<script>alert('Gagal! Anda tidak bisa menghapus akun sendiri saat sedang login.'); window.location='data_admin.php';</script>";
        } else {
            mysqli_query($conn, "DELETE FROM admin WHERE id_admin = '$id'");
            echo "<script>alert('Data Admin Dihapus'); window.location='data_admin.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen Data Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333; }

        /* Sidebar Style */
        .sidebar { position: fixed; top: 0; left: -250px; width: 250px; height: 100%; background: #343a40; box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1); transition: 0.3s ease; z-index: 1000; }
        .sidebar.active { left: 0; }
        .sidebar h2 { margin-top: 20px; color: #fff; text-align: center; font-size: 20px; padding-bottom: 20px; border-bottom: 1px solid #4b545c; }
        .sidebar a { display: block; padding: 15px 20px; text-decoration: none; color: #c2c7d0; border-bottom: 1px solid #4b545c; transition: 0.2s; }
        .sidebar a:hover { background: #494e53; color: #fff; }
        .logout { background: #dc3545; color: white !important; margin-top: 20px; text-align: center; }

        /* Main Content */
        .main-content { padding: 20px; transition: margin-left 0.3s ease; margin-left: 0; }
        .main-content.shift { margin-left: 250px; }

        .toggle-btn { font-size: 24px; cursor: pointer; color: #333; display: inline-block; margin-bottom: 20px; padding: 5px 10px; background: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        
        .container { max-width: 1200px; margin: 0 auto 20px; }
        .card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); margin-bottom: 30px; }
        
        h3 { margin-top: 0; color: #444; border-left: 5px solid #007bff; padding-left: 10px; }
        hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }

        /* Table & Buttons */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
        table th, table td { border: 1px solid #ddd; padding: 12px 10px; text-align: left; }
        table th { background-color: #007bff; color: white; }
        table tr:nth-child(even) { background-color: #f9f9f9; }

        .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; color: white; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-green { background-color: #28a745; }
        .btn-blue { background-color: #007bff; }
        .btn-red { background-color: #dc3545; }
        .btn-grey { background-color: #6c757d; }
        
        /* Modal */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 25px; border: 1px solid #888; width: 50%; border-radius: 10px; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }

        /* Form */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    </style>

    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
            document.querySelector(".main-content").classList.toggle("shift");
        }

        // --- JS MODAL ADMIN (Hanya akan dipanggil jika tombol muncul) ---
        function bukaModalTambah() {
            document.getElementById('modalAdmin').style.display = 'block';
            document.getElementById('modal_title').innerText = "Tambah Admin Baru";
            document.getElementById('formAdmin').reset();
            document.getElementById('id_admin').value = '';
            
            // Wajib password kalau tambah baru
            document.getElementById('password').required = true;
            document.getElementById('help_pass').style.display = 'none';

            let btn = document.getElementById('btn_submit');
            btn.name = "simpan_admin";
            btn.innerText = "Simpan Admin";
        }

        function editAdmin(id, nama, user, email, level) {
            document.getElementById('modalAdmin').style.display = 'block';
            document.getElementById('modal_title').innerText = "Edit Data Admin";

            document.getElementById('id_admin').value = id;
            document.getElementById('nama_admin').value = nama;
            document.getElementById('username').value = user;
            document.getElementById('email').value = email;
            document.getElementById('level').value = level;

            // Password tidak wajib saat edit
            document.getElementById('password').value = ''; 
            document.getElementById('password').required = false;
            document.getElementById('help_pass').style.display = 'block';

            let btn = document.getElementById('btn_submit');
            btn.name = "update_admin";
            btn.innerText = "Update Admin";
        }

        function tutupModal() {
            document.getElementById('modalAdmin').style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                tutupModal();
            }
        }
    </script>
</head>

<body>

    <div class="sidebar">
        <h2>Panel Admin</h2>
        <p style="color:#aaa; text-align:center; font-size:12px;">
            Halo, <?= $_SESSION['nama_admin'] ?? 'Admin'; ?> <br>
            (<?= ucfirst($level_user); ?>)
        </p>
        
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="data_admin.php" style="background:#494e53; color:white; border-left: 4px solid #007bff;">Data Admin</a>
        <a href="data_wisata.php">Data Wisata</a>
        
        <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </div>

    <div class="main-content">
        <span class="toggle-btn" onclick="toggleSidebar()">☰ Menu</span>

        <div class="container">
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Data Administrator</h3>
                    
                    <?php if ($level_user == 'superadmin') { ?>
                        <button onclick="bukaModalTambah()" class="btn btn-green">+ Tambah Admin</button>
                    <?php } ?>
                </div>
                
                <hr>

                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Level</th>
                                <?php if ($level_user == 'superadmin') { ?>
                                    <th width="150">Aksi</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "SELECT * FROM admin ORDER BY level DESC, nama_admin ASC");
                            while ($d = mysqli_fetch_array($query)) {
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($d['nama_admin']); ?></td>
                                    <td><?= htmlspecialchars($d['username']); ?></td>
                                    <td><?= htmlspecialchars($d['email']); ?></td>
                                    <td>
                                        <?php 
                                        if($d['level'] == 'superadmin'){
                                            echo "<span style='background:#ffc107; padding:2px 6px; border-radius:4px; font-size:12px; font-weight:bold;'>Superadmin</span>";
                                        } else {
                                            echo "<span style='background:#17a2b8; color:white; padding:2px 6px; border-radius:4px; font-size:12px;'>Admin</span>";
                                        }
                                        ?>
                                    </td>
                                    
                                    <?php if ($level_user == 'superadmin') { ?>
                                    <td>
                                        <button class="btn btn-blue" style="padding: 5px 10px; font-size:12px;"
                                            onclick="editAdmin(
                                           '<?= $d['id_admin'] ?>',
                                           '<?= addslashes($d['nama_admin']) ?>',
                                           '<?= addslashes($d['username']) ?>',
                                           '<?= addslashes($d['email']) ?>',
                                           '<?= $d['level'] ?>'
                                          )">Edit</button>

                                        <a href="?hapus_admin=<?= $d['id_admin']; ?>" class="btn btn-red" style="padding: 5px 10px; font-size:12px;" onclick="return confirm('Yakin hapus admin ini?')">Hapus</a>
                                    </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if ($level_user == 'superadmin') { ?>
    <div id="modalAdmin" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="tutupModal()">&times;</span>
            <h3 id="modal_title">Form Admin</h3>
            <hr>
            <form action="" method="POST" id="formAdmin">
                <input type="hidden" name="id_admin" id="id_admin">
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_admin" id="nama_admin" required>
                </div>

                <div style="display:flex; gap:15px;">
                    <div class="form-group" style="flex:1;">
                        <label>Username</label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password" id="password" placeholder="Masukkan password">
                    <small id="help_pass" style="color:red; display:none;">*Kosongkan jika tidak ingin mengubah password</small>
                </div>

                <div class="form-group">
                    <label>Level Akses</label>
                    <select name="level" id="level" required>
                        <option value="admin">Admin (Hanya Lihat)</option>
                        <option value="superadmin">Superadmin (Akses Penuh)</option>
                    </select>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" onclick="tutupModal()" class="btn btn-grey">Batal</button>
                    <button type="submit" name="simpan_admin" id="btn_submit" class="btn btn-green">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <?php } ?>

</body>
</html>
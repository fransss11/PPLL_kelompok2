<?php
session_start();
include "../connect.php";

// ==========================================
// BAGIAN 1: LOGIC PHP (CRUD)
// ==========================================

// --- A. CRUD KATEGORI ---

// 1. Tambah Kategori
if (isset($_POST['simpan_kategori'])) {
    $nama_kat = $_POST['nama_kategori'];
    $query = "INSERT INTO kategori_wisata (nama_kategori) VALUES ('$nama_kat')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Kategori Berhasil Ditambah'); window.location='data_wisata.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah kategori');</script>";
    }
}

// 2. Update Kategori
if (isset($_POST['update_kategori'])) {
    $id_kat   = $_POST['id_kategori_edit'];
    $nama_kat = $_POST['nama_kategori'];
    $query = "UPDATE kategori_wisata SET nama_kategori='$nama_kat' WHERE id_kategori='$id_kat'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Kategori Berhasil Diupdate'); window.location='data_wisata.php';</script>";
    }
}

// 3. Hapus Kategori
if (isset($_GET['hapus_kategori'])) {
    $id = $_GET['hapus_kategori'];
    // Cek apakah dipakai di tabel wisata
    $cek = mysqli_query($conn, "SELECT * FROM wisata WHERE id_kategori='$id'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal! Kategori ini sedang digunakan oleh data Wisata. Hapus data wisatanya terlebih dahulu.'); window.location='data_wisata.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM kategori_wisata WHERE id_kategori = '$id'");
        echo "<script>alert('Kategori Dihapus'); window.location='data_wisata.php';</script>";
    }
}


// --- B. CRUD WISATA ---

// 1. Tambah Wisata
if (isset($_POST['simpan_wisata'])) {
    $id_kategori = $_POST['id_kategori'];
    $nama        = $_POST['nama_wisata'];
    $deskripsi   = $_POST['deskripsi'];
    $lokasi      = $_POST['lokasi_wisata'];
    $jam         = $_POST['jam_operasi'];
    $harga       = $_POST['harga'];
    $lat         = $_POST['latitude'];
    $long        = $_POST['longitude'];

    $query = "INSERT INTO wisata (id_kategori, nama_wisata, deskripsi, lokasi_wisata, jam_operasi, harga, latitude, longitude) 
              VALUES ('$id_kategori', '$nama', '$deskripsi', '$lokasi', '$jam', '$harga', '$lat', '$long')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data Wisata Berhasil Ditambahkan'); window.location='data_wisata.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}

// 2. Update Wisata
if (isset($_POST['update_wisata'])) {
    $id          = $_POST['id_wisata'];
    $id_kategori = $_POST['id_kategori'];
    $nama        = $_POST['nama_wisata'];
    $deskripsi   = $_POST['deskripsi'];
    $lokasi      = $_POST['lokasi_wisata'];
    $jam         = $_POST['jam_operasi'];
    $harga       = $_POST['harga'];
    $lat         = $_POST['latitude'];
    $long        = $_POST['longitude'];

    $query = "UPDATE wisata SET 
              id_kategori='$id_kategori', nama_wisata='$nama', deskripsi='$deskripsi', 
              lokasi_wisata='$lokasi', jam_operasi='$jam', harga='$harga', 
              latitude='$lat', longitude='$long' 
              WHERE id_wisata='$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data Wisata Diupdate'); window.location='data_wisata.php';</script>";
    }
}

// 3. Hapus Wisata
if (isset($_GET['hapus_wisata'])) {
    $id = $_GET['hapus_wisata'];
    $query = "DELETE FROM wisata WHERE id_wisata = '$id'";
    mysqli_query($conn, $query);
    echo "<script>alert('Data Wisata Dihapus'); window.location='data_wisata.php';</script>";
}


// --- C. CRUD GAMBAR ---

// 1. Upload Gambar
if (isset($_POST['upload_gambar'])) {
    $id_wisata = $_POST['id_wisata_img'];
    $filename = $_FILES['file_gambar']['name'];
    $tmp_name = $_FILES['file_gambar']['tmp_name'];

    $new_filename = time() . '_' . $filename;
    $tujuan = "../images/" . $new_filename;

    if (move_uploaded_file($tmp_name, $tujuan)) {
        $query = "INSERT INTO wisata_gambar (id_wisata, file_gambar) VALUES ('$id_wisata', '$new_filename')";
        mysqli_query($conn, $query);
        echo "<script>alert('Gambar Berhasil Diupload'); window.location='data_wisata.php';</script>";
    } else {
        echo "<script>alert('Gagal Upload File');</script>";
    }
}

// 2. Hapus Gambar
if (isset($_GET['hapus_gambar'])) {
    $id_img = $_GET['hapus_gambar'];
    $q_file = mysqli_query($conn, "SELECT file_gambar FROM wisata_gambar WHERE id_gambar='$id_img'");
    $f = mysqli_fetch_assoc($q_file);
    $path = "../images/" . $f['file_gambar'];
    if (file_exists($path)) {
        unlink($path);
    }

    mysqli_query($conn, "DELETE FROM wisata_gambar WHERE id_gambar='$id_img'");
    echo "<script>alert('Gambar Dihapus'); window.location='data_wisata.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen Data Wisata</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        /* Sidebar Style */
        .sidebar {
            position: fixed;
            top: 0;
            left: -230px;
            width: 230px;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease;
            z-index: 1000;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar h2 {
            margin-top: 20px;
            color: #0056b3;
            text-align: center;
        }

        .sidebar a {
            display: block;
            padding: 10px 20px;
            margin-top: 5px;
            text-decoration: none;
            color: #222;
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

        /* --- PERBAIKAN TOGGLE & CONTENT DISINI --- */
        .main-content {
            padding: 20px;
            transition: margin-left 0.3s ease;
            /* Transisi margin */
            margin-left: 0;
        }

        .main-content.shift {
            margin-left: 230px;
            /* Geser konten (dan tombol) ke kanan */
        }

        .toggle-btn {
            font-size: 30px;
            cursor: pointer;
            color: #333;
            display: inline-block;
            /* Agar tombol menjadi blok konten */
            margin-bottom: 20px;
            /* Jarak ke bawah */
            padding: 5px;
            background: #fff;
            /* Sedikit background putih */
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .toggle-btn:hover {
            background: #eee;
        }

        /* ------------------------------------------- */

        /* Container & Card */
        .container {
            max-width: 1200px;
            margin: 0 auto 20px;
        }

        /* Margin top 0 karena sudah ada di toggle-btn */
        .card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        h3 {
            margin-top: 0;
            color: #444;
            border-left: 5px solid #007bff;
            padding-left: 10px;
        }

        hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        /* --- STYLING MODAL (POP UP) --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 25px;
            border: 1px solid #888;
            width: 60%;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s;
        }

        /* Modal kecil untuk kategori */
        .modal-small {
            width: 30%;
            margin-top: 15%;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover {
            color: #000;
        }

        /* Form & Table */
        .form-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-green {
            background-color: #28a745;
        }

        .btn-blue {
            background-color: #007bff;
        }

        .btn-red {
            background-color: #dc3545;
        }

        .btn-grey {
            background-color: #6c757d;
        }

        .btn:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #0056b3;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>

    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
            document.querySelector(".main-content").classList.toggle("shift");
        }

        // --- JS MODAL WISATA ---
        function bukaModalTambah() {
            document.getElementById('modalWisata').style.display = 'block';
            document.getElementById('modal_title').innerText = "Tambah Data Wisata";
            document.getElementById('formWisata').reset();
            document.getElementById('id_wisata').value = '';

            let btn = document.getElementById('btn_submit');
            btn.name = "simpan_wisata";
            btn.innerText = "Simpan Data";
            btn.className = "btn btn-green";
        }

        function editWisata(id, id_kat, nama, deskripsi, lokasi, jam, harga, lat, long) {
            document.getElementById('modalWisata').style.display = 'block';
            document.getElementById('modal_title').innerText = "Edit Data Wisata";

            document.getElementById('id_wisata').value = id;
            document.getElementById('id_kategori').value = id_kat;
            document.getElementById('nama_wisata').value = nama;
            document.getElementById('deskripsi').value = deskripsi;
            document.getElementById('lokasi_wisata').value = lokasi;
            document.getElementById('jam_operasi').value = jam;
            document.getElementById('harga').value = harga;
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = long;

            let btn = document.getElementById('btn_submit');
            btn.name = "update_wisata";
            btn.innerText = "Update Data";
            btn.className = "btn btn-blue";
        }

        // --- JS MODAL KATEGORI ---
        function bukaModalKategori() {
            document.getElementById('modalKategori').style.display = 'block';
            document.getElementById('modal_kat_title').innerText = "Tambah Kategori";
            document.getElementById('formKategori').reset();
            document.getElementById('id_kategori_edit').value = '';

            let btn = document.getElementById('btn_submit_kat');
            btn.name = "simpan_kategori";
            btn.innerText = "Simpan";
            btn.className = "btn btn-green";
        }

        function editKategori(id, nama) {
            document.getElementById('modalKategori').style.display = 'block';
            document.getElementById('modal_kat_title').innerText = "Edit Kategori";
            document.getElementById('id_kategori_edit').value = id;
            document.getElementById('nama_kategori_input').value = nama;

            let btn = document.getElementById('btn_submit_kat');
            btn.name = "update_kategori";
            btn.innerText = "Update";
            btn.className = "btn btn-blue";
        }

        function tutupModal(idModal) {
            document.getElementById(idModal).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }
    </script>
</head>

<body>

    <div class="sidebar">
        <h2>Menu Admin</h2>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="data_admin.php">Data Admin</a>
        <a href="data_wisata.php" style="background:#007bff; color:white;">Data Wisata</a>
        <hr>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="main-content">
        <span class="toggle-btn" onclick="toggleSidebar()">☰</span>

        <div class="container">

            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Data Kategori</h3>
                    <button onclick="bukaModalKategori()" class="btn btn-green">+ Kategori Baru</button>
                </div>
                <hr>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Kategori</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $noK = 1;
                            $queryK = mysqli_query($conn, "SELECT * FROM kategori_wisata ORDER BY id_kategori ASC");
                            while ($k = mysqli_fetch_array($queryK)) {
                            ?>
                                <tr>
                                    <td><?= $noK++; ?></td>
                                    <td><?= $k['nama_kategori']; ?></td>
                                    <td>
                                        <button class="btn btn-blue" style="padding: 5px 10px; font-size:12px;"
                                            onclick="editKategori('<?= $k['id_kategori'] ?>', '<?= addslashes($k['nama_kategori']) ?>')">
                                            Edit
                                        </button>
                                        <a href="?hapus_kategori=<?= $k['id_kategori']; ?>" class="btn btn-red" style="padding: 5px 10px; font-size:12px;"
                                            onclick="return confirm('Yakin hapus kategori ini? Pastikan tidak ada data Wisata yang menggunakannya.')">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Daftar Wisata</h3>
                    <button onclick="bukaModalTambah()" class="btn btn-green">+ Tambah Wisata</button>
                </div>

                <hr>

                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Wisata</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Harga</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $queryW = mysqli_query($conn, "SELECT * FROM wisata JOIN kategori_wisata ON wisata.id_kategori = kategori_wisata.id_kategori ORDER BY id_wisata DESC");
                            while ($w = mysqli_fetch_array($queryW)) {
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><b><?= $w['nama_wisata']; ?></b></td>
                                    <td><?= $w['nama_kategori']; ?></td>
                                    <td><?= $w['lokasi_wisata']; ?></td>
                                    <td><?= $w['harga'] == NULL ? 'Gratis/Belum' : 'Rp ' . number_format($w['harga']); ?></td>
                                    <td>
                                        <button class="btn btn-blue" style="padding: 5px 10px; font-size:12px;"
                                            onclick="editWisata(
                                       '<?= $w['id_wisata'] ?>',
                                       '<?= $w['id_kategori'] ?>',
                                       '<?= addslashes($w['nama_wisata']) ?>',
                                       '<?= addslashes($w['deskripsi']) ?>',
                                       '<?= addslashes($w['lokasi_wisata']) ?>',
                                       '<?= $w['jam_operasi'] ?>',
                                       '<?= $w['harga'] ?>',
                                       '<?= $w['latitude'] ?>',
                                       '<?= $w['longitude'] ?>'
                                   )">Edit</button>

                                        <a href="?hapus_wisata=<?= $w['id_wisata']; ?>" class="btn btn-red" style="padding: 5px 10px; font-size:12px;" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="border-top: 5px solid #ffc107;">
                <h3>Manajemen Galeri / Gambar</h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-row" style="align-items: flex-end;">
                        <div class="form-group">
                            <label>Pilih Wisata</label>
                            <select name="id_wisata_img" required>
                                <option value="">- Pilih Wisata Tujuan -</option>
                                <?php
                                // Query ulang untuk dropdown gambar
                                $wisataList = mysqli_query($conn, "SELECT id_wisata, nama_wisata FROM wisata ORDER BY nama_wisata ASC");
                                while ($wl = mysqli_fetch_array($wisataList)) {
                                    echo "<option value='" . $wl['id_wisata'] . "'>" . $wl['nama_wisata'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Upload File Gambar</label>
                            <input type="file" name="file_gambar" accept=".jpg, .jpeg, .png" required>
                        </div>
                        <div class="form-group" style="flex: 0;">
                            <button type="submit" name="upload_gambar" class="btn btn-blue" style="margin-bottom:2px;">Upload</button>
                        </div>
                    </div>
                </form>
                <hr>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Wisata</th>
                                <th>Preview</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $noG = 1;
                            $queryG = mysqli_query($conn, "SELECT * FROM wisata_gambar JOIN wisata ON wisata_gambar.id_wisata = wisata.id_wisata ORDER BY id_gambar DESC");
                            if (mysqli_num_rows($queryG) > 0) {
                                while ($g = mysqli_fetch_array($queryG)) {
                            ?>
                                    <tr>
                                        <td><?= $noG++; ?></td>
                                        <td><?= $g['nama_wisata']; ?></td>
                                        <td><img src="../images/<?= $g['file_gambar']; ?>" alt="Img" style="width: 80px; height: 50px; object-fit: cover; border-radius:4px;"></td>
                                        <td>
                                            <a href="?hapus_gambar=<?= $g['id_gambar']; ?>" class="btn btn-red" style="padding: 5px 10px; font-size:12px;" onclick="return confirm('Hapus gambar?')">Hapus</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center;'>Belum ada data gambar.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div id="modalWisata" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="tutupModal('modalWisata')">&times;</span>
            <h3 id="modal_title">Tambah Data Wisata</h3>
            <hr>
            <form action="" method="POST" id="formWisata">
                <input type="hidden" name="id_wisata" id="id_wisata">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Wisata</label>
                        <input type="text" name="nama_wisata" id="nama_wisata" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" id="id_kategori" required>
                            <option value="">- Pilih Kategori -</option>
                            <?php
                            $kat2 = mysqli_query($conn, "SELECT * FROM kategori_wisata");
                            while ($k2 = mysqli_fetch_array($kat2)) {
                                echo "<option value='" . $k2['id_kategori'] . "'>" . $k2['nama_kategori'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi_wisata" id="lokasi_wisata">
                    </div>
                    <div class="form-group">
                        <label>Jam Operasi</label>
                        <input type="text" name="jam_operasi" id="jam_operasi">
                    </div>
                    <div class="form-group">
                        <label>Harga Tiket (Rp)</label>
                        <input type="number" name="harga" id="harga">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="latitude">
                    </div>
                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="longitude">
                    </div>
                </div>
                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" onclick="tutupModal('modalWisata')" class="btn btn-grey">Batal</button>
                    <button type="submit" name="simpan_wisata" id="btn_submit" class="btn btn-green">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalKategori" class="modal">
        <div class="modal-content modal-small">
            <span class="close-btn" onclick="tutupModal('modalKategori')">&times;</span>
            <h3 id="modal_kat_title">Tambah Kategori</h3>
            <hr>
            <form action="" method="POST" id="formKategori">
                <input type="hidden" name="id_kategori_edit" id="id_kategori_edit">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori_input" required placeholder="Contoh: Pegunungan">
                </div>
                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" onclick="tutupModal('modalKategori')" class="btn btn-grey">Batal</button>
                    <button type="submit" name="simpan_kategori" id="btn_submit_kat" class="btn btn-green">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
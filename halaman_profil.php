<?php
require_once 'connect.php';

$teamMembers = [
    [
        'name' => 'Narendra Wardianto',
        'nim' => '220411100078',
        'jobdesk' => 'Halaman Profil',
        'github' => 'https://github.com/naren56',
    ],
    [
        'name' => 'Lisda Lanchira Syahijan',
        'nim' => '220411100091',
        'jobdesk' => 'Dashboard Sistem',
        'github' => 'https://github.com/LisdaLanchiraSyahijan',
    ],
    [
        'name' => 'Zalikna Ries Fentryca',
        'nim' => '220411100197',
        'jobdesk' => 'Daftar Wisata',
        'github' => 'https://github.com/Fentryca',
    ],
    [
        'name' => 'Andre',
        'nim' => '220411100028',
        'jobdesk' => 'Landing Pages',
        'github' => 'https://github.com/andre22-028',
    ],
    [
        'name' => 'Frans Andreas Pasaribu',
        'nim' => '220411100135',
        'jobdesk' => 'Halaman Detail Wisata',
        'github' => 'https://github.com/fransss11',
    ],
    [
        'name' => 'Surya Eka Santoso',
        'nim' => '220411100149',
        'jobdesk' => 'Database dan Halaman CRUD Wisata',
        'github' => 'https://github.com/suryaekasantoso',
    ],
    [
        'name' => 'Fahmi Hidayatullah',
        'nim' => '220411100117',
        'jobdesk' => 'Login Admin',
        'github' => 'https://github.com/fahmiop',
    ],
    [
        'name' => 'Mahardika Dwi Sandra',
        'nim' => '220411100195',
        'jobdesk' => 'CRUD Data Admin dan Deploy',
        'github' => 'https://github.com/22-195-mahardika',
    ],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Tim PPLL</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
        /* Animasi untuk efek gambar profil */
        @keyframes animateProfileImage {
            0% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(360deg);
            }
            50% {
                transform: scale(1.1);
            }
            75% {
                transform: scale(1.2);
            }
            100% {
                transform: rotate(0deg);
                scale(1);
            }
        }

        /* Animasi untuk teks */
        @keyframes animateText {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            50% {
                opacity: 1;
                transform: translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .profile-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.10);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s ease;
            text-align: center;
            animation: animateText 4s ease-in-out infinite; /* Animasi teks */
        }

        .profile-card:hover {
            transform: translateY(-4px);
        }

        .profile-img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-bottom: 15px;
            animation: animateProfileImage 10s linear infinite; /* Animasi gambar profil */
        }

        .profile-body h5 {
            font-size: 18px;
            font-weight: 600;
            margin: 10px 0;
        }

        .profile-body p {
            font-size: 14px;
            color: #555;
        }

        .github-link {
            color: #333;
            font-weight: bold;
            text-decoration: none;
        }

        .github-link:hover {
            color: #F96D00;
        }

        /* Animasi untuk hover card */
        .profile-card:hover {
            animation: scaleCard 0.4s ease-out infinite alternate;
        }

        @keyframes scaleCard {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(1.05);
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">PPLL<span>Wisata</span></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav">
                <span class="oi oi-menu"></span> Menu
            </button>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item active"><a href="halaman_profil.php" class="nav-link">Profil</a></li>
                    <li class="nav-item"><a href="daftar_wisata.php" class="nav-link">Wisata</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="ftco-section bg-light py-5">
        <div class="container">

            <div class="row justify-content-center mb-5">
                <div class="col-md-12 text-center">
                    <h2 class="section-title">Profil Tim PPLL</h2>
                    <p>Berikut adalah anggota tim yang berkontribusi dalam pengembangan proyek Wisata Bangkalan.</p>
                </div>
            </div>

            <div class="row">
                <?php foreach ($teamMembers as $member): ?>
                    <div class="col-md-3 mb-4">
                        <div class="profile-card">
                            <img src="images/anonim.jpg" alt="Profile Picture" class="profile-img">

                            <div class="profile-body">
                                <h5><?= htmlspecialchars($member['name']) ?></h5>
                                <p><strong>NIM:</strong> <?= htmlspecialchars($member['nim']) ?></p>
                                <p><strong>Jobdesk:</strong> <?= htmlspecialchars($member['jobdesk']) ?></p>

                                <a href="<?= htmlspecialchars($member['github']) ?>"
                                   target="_blank"
                                   class="github-link">GitHub Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <footer class="ftco-footer bg-bottom ftco-no-pt" style="background-image: url('images/bg_3.jpg');">
        <div class="container text-center py-4">
            <p>&copy; <script>document.write(new Date().getFullYear());</script> PPLL KELOMPOK 2</p>
        </div>
    </footer>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>
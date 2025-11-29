<?php
require_once 'connect.php';

$categoryId = filter_input(INPUT_GET, 'kategori', FILTER_VALIDATE_INT);
$keyword = trim((string) filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS));

$query = 'SELECT w.id_wisata, w.nama_wisata, w.deskripsi, w.lokasi_wisata, w.harga, w.latitude, w.longitude, w.id_kategori, k.nama_kategori,
								 (SELECT file_gambar FROM wisata_gambar WHERE id_wisata = w.id_wisata ORDER BY id_gambar ASC LIMIT 1) AS thumbnail
					FROM wisata w
					LEFT JOIN kategori_wisata k ON w.id_kategori = k.id_kategori';

$conditions = [];
$params = [];
$types = '';

if ($categoryId) {
		$conditions[] = 'w.id_kategori = ?';
		$params[] = $categoryId;
		$types .= 'i';
}

if ($keyword !== '') {
		$conditions[] = '(w.nama_wisata LIKE ? OR w.deskripsi LIKE ? OR w.lokasi_wisata LIKE ?)';
		$likeValue = '%' . $keyword . '%';
		$params[] = $likeValue;
		$params[] = $likeValue;
		$params[] = $likeValue;
		$types .= 'sss';
}

if ($conditions) {
		$query .= ' WHERE ' . implode(' AND ', $conditions);
}

$query .= ' ORDER BY w.nama_wisata ASC';

$stmt = $conn->prepare($query);

if (!$stmt) {
		die('Terjadi kesalahan pada query.');
}

if ($params) {
		$stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$wisataList = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$kategoriResult = $conn->query('SELECT id_kategori, nama_kategori FROM kategori_wisata ORDER BY nama_kategori ASC');
$kategoriList = $kategoriResult ? $kategoriResult->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();

function formatExcerpt(?string $text, int $limit = 170): string
{
		$clean = trim($text ?? '');
		if ($clean === '') {
				return 'Belum ada deskripsi.';
		}

		if (mb_strlen($clean) <= $limit) {
				return $clean;
		}

		$trimmed = mb_substr($clean, 0, $limit);
		return rtrim($trimmed) . '...';
}

function formatPriceList(?string $price): string
{
		if ($price === null || $price === '') {
				return 'Hubungi pengelola';
		}

		return 'Rp ' . number_format((float) $price, 0, ',', '.');
}

$resolveImagePath = static function (?string $path): string {
	$trimmed = trim((string) $path);

	if ($trimmed === '') {
		return 'images/bg_1.jpg';
	}

	if (preg_match('/^(https?:)?\/\//', $trimmed)) {
		return $trimmed;
	}

	if ($trimmed[0] === '/') {
		return ltrim($trimmed, '/');
	}

	if (strpos($trimmed, 'images/') === 0 || strpos($trimmed, 'uploads/') === 0) {
		return $trimmed;
	}

	return 'images/' . $trimmed;
};
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Wisata Bangkalan - Daftar Wisata</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Arizonia&display=swap" rel="stylesheet">

		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

		<link rel="stylesheet" href="css/animate.css">
		<link rel="stylesheet" href="css/owl.carousel.min.css">
		<link rel="stylesheet" href="css/owl.theme.default.min.css">
		<link rel="stylesheet" href="css/magnific-popup.css">
		<link rel="stylesheet" href="css/bootstrap-datepicker.css">
		<link rel="stylesheet" href="css/jquery.timepicker.css">
		<link rel="stylesheet" href="css/flaticon.css">
		<link rel="stylesheet" href="css/style.css">

		<style>
			.listing-hero { background-image:url('images/bg_2.jpg'); background-size:cover; background-position:center; }
			.listing-card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 10px 28px rgba(0,0,0,0.08); transition:transform 0.2s ease; }
			.listing-card:hover { transform:translateY(-4px); }
			.listing-thumb { height:220px; background-size:cover; background-position:center; }
			.listing-body { padding:20px; }
			.listing-footer { padding:16px 20px; background:#f8f9fa; color:#555; display:flex; justify-content:space-between; align-items:center; }
			.listing-filter { background:#fff; padding:18px; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.05); margin-bottom:30px; }
			.badge-category { background:#f96d00; color:#fff; padding:6px 12px; border-radius:999px; font-size:0.85rem; }
			.empty-state { text-align:center; padding:60px 20px; background:#fff; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,0.04); }

			@media (max-width: 767.98px) {
				.listing-thumb { height:180px; }
				.listing-body { padding:16px; }
				.listing-footer { flex-direction:column; align-items:flex-start; gap:10px; }
			}
		</style>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
			<div class="container">
				<a class="navbar-brand" href="index.php">PPLL<span>Wisata</span></a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="oi oi-menu"></span> Menu
				</button>

				<div class="collapse navbar-collapse" id="ftco-nav">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
						<li class="nav-item"><a href="halaman_profil.php" class="nav-link">Profil</a></li>
						<li class="nav-item active"><a href="daftar_wisata.php" class="nav-link">Wisata</a></li>
					</ul>
				</div>
			</div>
		</nav>

		<section class="hero-wrap hero-wrap-2 js-fullheight listing-hero">
			<div class="overlay"></div>
			<div class="container">
				<div class="row no-gutters slider-text js-fullheight align-items-end justify-content-center">
					<div class="col-md-9 ftco-animate pb-5 text-center">
						<h1 class="mb-2 bread">Daftar Wisata Bangkalan</h1>
						<p class="breadcrumbs"><span class="mr-2"><a href="index.php">Home</a></span> <span>Wisata</span></p>
					</div>
				</div>
			</div>
		</section>

		<section class="ftco-section bg-light">
			<div class="container">
				<div class="listing-filter ftco-animate">
					<form class="form-row" method="get" action="">
						<div class="form-group col-md-4 col-sm-12">
							<label for="kategori" class="sr-only">Kategori</label>
							<select name="kategori" id="kategori" class="form-control">
								<option value="">Semua Kategori</option>
								<?php foreach ($kategoriList as $kategori): ?>
									<option value="<?php echo (int) $kategori['id_kategori']; ?>" <?php echo ($categoryId === (int) $kategori['id_kategori']) ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($kategori['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group col-md-6 col-sm-12">
							<label for="q" class="sr-only">Pencarian</label>
							<input type="text" name="q" id="q" class="form-control" placeholder="Cari nama wisata, lokasi, atau deskripsi" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>">
						</div>
						<div class="form-group col-md-2 col-sm-12 d-flex align-items-end">
							<button type="submit" class="btn btn-primary btn-block">Cari</button>
						</div>
					</form>
				</div>

				<div class="row">
					<?php if ($wisataList): ?>
						<?php foreach ($wisataList as $wisata): ?>
							<div class="col-md-4 mb-4 ftco-animate">
								<div class="listing-card h-100 d-flex flex-column">
									  <div class="listing-thumb" style="background-image:url('<?php echo htmlspecialchars($resolveImagePath($wisata['thumbnail']), ENT_QUOTES, 'UTF-8'); ?>');"></div>
									<div class="listing-body flex-grow-1">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<h5 class="mb-0"><?php echo htmlspecialchars($wisata['nama_wisata'], ENT_QUOTES, 'UTF-8'); ?></h5>
											<?php if (!empty($wisata['nama_kategori'])): ?>
												<span class="badge-category"><?php echo htmlspecialchars($wisata['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?></span>
											<?php endif; ?>
										</div>
										<p class="text-muted mb-2"><span class="fa fa-map-marker"></span> <?php echo htmlspecialchars($wisata['lokasi_wisata'] ?? 'Bangkalan', ENT_QUOTES, 'UTF-8'); ?></p>
										<p class="mb-0"><?php echo htmlspecialchars(formatExcerpt($wisata['deskripsi']), ENT_QUOTES, 'UTF-8'); ?></p>
									</div>
									<div class="listing-footer">
										<span><?php echo htmlspecialchars(formatPriceList($wisata['harga']), ENT_QUOTES, 'UTF-8'); ?></span>
										<a href="detail_wisata.php?id=<?php echo (int) $wisata['id_wisata']; ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="col-12">
							<div class="empty-state">
								<h4>Belum ada wisata yang sesuai.</h4>
								<p>Coba ubah filter kategori atau kata kunci pencarian.</p>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<footer class="ftco-footer bg-bottom ftco-no-pt" style="background-image: url('images/bg_3.jpg');">
			<div class="container">
				<div class="row mb-5">
					<div class="col-md pt-5">
						<div class="ftco-footer-widget pt-md-5 mb-4">
							<h2 class="ftco-heading-2">About</h2>
							<p>Wisata Kabupaten Bangkalan hadir untuk memperkenalkan pesona alam, budaya, dan kearifan lokal yang dimiliki Bangkalan, Madura.</p>
							<ul class="ftco-footer-social list-unstyled float-md-left float-lft">
								<li class="ftco-animate"><a href="#"><span class="fa fa-twitter"></span></a></li>
								<li class="ftco-animate"><a href="#"><span class="fa fa-facebook"></span></a></li>
								<li class="ftco-animate"><a href="#"><span class="fa fa-instagram"></span></a></li>
							</ul>
						</div>
					</div>
					<div class="col-md pt-5 border-left">
						<div class="ftco-footer-widget pt-md-5 mb-4 ml-md-5">
							<h2 class="ftco-heading-2">Information</h2>
							<ul class="list-unstyled">
								<li><a href="#" class="py-2 d-block">Online Enquiry</a></li>
								<li><a href="#" class="py-2 d-block">General Enquiries</a></li>
								<li><a href="#" class="py-2 d-block">Booking Conditions</a></li>
							</ul>
						</div>
					</div>
					<div class="col-md pt-5 border-left">
						<div class="ftco-footer-widget pt-md-5 mb-4">
							<h2 class="ftco-heading-2">Experience</h2>
							<ul class="list-unstyled">
								<li><a href="#" class="py-2 d-block">Adventure</a></li>
								<li><a href="#" class="py-2 d-block">Beach</a></li>
								<li><a href="#" class="py-2 d-block">Nature</a></li>
							</ul>
						</div>
					</div>
					<div class="col-md pt-5 border-left">
						<div class="ftco-footer-widget pt-md-5 mb-4">
							<h2 class="ftco-heading-2">Contact Me</h2>
							<div class="block-23 mb-3">
								<ul>
									<li><span class="icon fa fa-map-marker"></span><span class="text">Indonesia, Jawa Timur, Kabupaten Bangkalan</span></li>
									<li><a href="#"><span class="icon fa fa-phone"></span><span class="text">+62 821-4344-8678</span></a></li>
									<li><a href="#"><span class="icon fa fa-paper-plane"></span><span class="text">narendra.wardianto56@gmail.com</span></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> by PPLL KELOMPOK 2</p>
					</div>
				</div>
			</div>
		</footer>


		<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" /><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00" /></svg></div>

		<script src="js/jquery.min.js"></script>
		<script src="js/jquery-migrate-3.0.1.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.easing.1.3.js"></script>
		<script src="js/jquery.waypoints.min.js"></script>
		<script src="js/jquery.stellar.min.js"></script>
		<script src="js/owl.carousel.min.js"></script>
		<script src="js/jquery.magnific-popup.min.js"></script>
		<script src="js/jquery.animateNumber.min.js"></script>
		<script src="js/bootstrap-datepicker.js"></script>
		<script src="js/scrollax.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
		<script src="js/google-map.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>

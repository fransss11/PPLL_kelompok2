<?php
require_once 'connect.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$detail = null;
$images = [];
$errorMessage = null;

if ($id) {
		$stmt = $conn->prepare('SELECT w.*, k.nama_kategori FROM wisata w LEFT JOIN kategori_wisata k ON w.id_kategori = k.id_kategori WHERE w.id_wisata = ? LIMIT 1');
		if ($stmt) {
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$result = $stmt->get_result();
				$detail = $result->fetch_assoc();
				$stmt->close();
		}

		if ($detail) {
				$stmtImages = $conn->prepare('SELECT file_gambar FROM wisata_gambar WHERE id_wisata = ? ORDER BY id_gambar ASC');
				if ($stmtImages) {
						$stmtImages->bind_param('i', $id);
						$stmtImages->execute();
						$resultImages = $stmtImages->get_result();
						while ($row = $resultImages->fetch_assoc()) {
								if (!empty($row['file_gambar'])) {
										$images[] = $row['file_gambar'];
								}
						}
						$stmtImages->close();
				}
		} else {
				$errorMessage = 'Wisata tidak ditemukan.';
		}
} else {
		$errorMessage = 'Wisata tidak ditemukan.';
}

$conn->close();

function formatPrice(?string $price): ?string
{
		if ($price === null || $price === '') {
				return null;
		}

		return 'Rp ' . number_format((float) $price, 0, ',', '.');
}

$normalizeCoordinate = static function ($value): ?float {
	if ($value === null) {
		return null;
	}

	$stringValue = trim((string) $value);

	if ($stringValue === '') {
		return null;
	}

	$stringValue = str_replace(',', '.', $stringValue);

	if (!is_numeric($stringValue)) {
		return null;
	}

	return (float) $stringValue;
};

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

$primaryImage = $images[0] ?? null;
$heroBackground = $resolveImagePath($primaryImage);
$categoryLabel = $detail['nama_kategori'] ?? 'Wisata';
$mapSrc = null;

if ($detail) {
	$latitude = $normalizeCoordinate($detail['latitude'] ?? null);
	$longitude = $normalizeCoordinate($detail['longitude'] ?? null);

	if ($latitude !== null && $longitude !== null) {
		$lat = number_format($latitude, 6, '.', '');
		$lng = number_format($longitude, 6, '.', '');
		$mapSrc = 'https://maps.google.com/maps?q=' . $lat . ',' . $lng . '&hl=id&z=15&output=embed';
	} else {
		$locationQuery = trim(($detail['nama_wisata'] ?? '') . ' ' . ($detail['lokasi_wisata'] ?? ''));
		if ($locationQuery !== '') {
			$mapSrc = 'https://www.google.com/maps?q=' . rawurlencode($locationQuery . ' Bangkalan') . '&output=embed';
		}
	}
}

$formattedPrice = $detail ? formatPrice($detail['harga'] ?? null) : null;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Wisata Bangkalan - Detail</title>
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
			/* small overrides for the detail layout */
			.detail-wrap { display:flex; gap:30px; margin-top: 20px; align-items:flex-start; }
			.detail-gallery { flex:2; }
			.detail-sidebar { flex:1; background:#fff; padding:20px; border-radius:6px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
			.detail-gallery .thumb { height: 480px; background-size: cover; background-position: center; border-radius:6px; }
			.detail-gallery .thumb-list { display:flex; flex-wrap:wrap; gap:10px; margin-top:12px; }
			.detail-gallery .thumb-list img { width:100px; height:80px; object-fit:cover; border-radius:4px; box-shadow:0 4px 10px rgba(0,0,0,0.08); }
			.detail-map iframe { width:100%; min-height:320px; border:0; border-radius:6px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }

			/* hero tweaks */
			#detail-hero{ background-image: url('images/bg_1.jpg'); background-size:cover; background-position:center; }
			#detail-hero .detail-price{ background: rgba(255,255,255,0.9); color:#222; padding:6px 10px; border-radius:4px; font-weight:700; }
			#detail-hero h1{ color:#fff; font-size:2.25rem; margin-top:0.5rem; }
			#detail-hero .detail-location{ color: rgba(255,255,255,0.95); }

			/* footer background */
			.ftco-footer.bg-bottom{ background-image: url('images/bg_3.jpg'); background-size:cover; background-position:center; }

			/* small screens: stack and increase touch targets */
			@media (max-width: 991.98px) {
				.detail-wrap { gap:18px; }
				.detail-gallery .thumb { height:360px; }
				.detail-sidebar { padding:18px; }
				.detail-map iframe { min-height:260px; }
				#detail-hero h1{ font-size:1.9rem; }
			}

			@media (max-width: 767.98px) {
				/* stack columns */
				.detail-wrap{ flex-direction: column; }
				.detail-gallery, .detail-sidebar{ width:100%; }
				.detail-gallery .thumb{ height:220px; border-radius:6px; }

				/* hero smaller and readable */
				#detail-hero { padding: 40px 0; }
				#detail-hero h1{ font-size:1.5rem; line-height:1.2; }
				#detail-hero .detail-price{ font-size:0.9rem; padding:5px 8px; }

				/* sidebar improvements for mobile */
				.detail-sidebar{ padding:14px; box-shadow:none; }
				.detail-sidebar .price-row{ flex-direction:column; align-items:flex-start; gap:6px; }

				/* make links/buttons easier to tap */
				.detail-sidebar a.btn, .btn{ display:inline-block; padding:10px 14px; font-size:1rem; }
			}

			/* accessibility helpers */
			.sr-only{ position: absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0; }
		</style>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
			<div class="container">
				<a class="navbar-brand" href="index.html">PPLL<span>Wisata</span></a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="oi oi-menu"></span> Menu
				</button>

				<div class="collapse navbar-collapse" id="ftco-nav">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
						<li class="nav-item"><a href="#" class="nav-link">Profil</a></li>
						<li class="nav-item"><a href="daftarwisata.html" class="nav-link">Wisata</a></li>
					</ul>
				</div>
			</div>
		</nav>

	<section class="hero-wrap hero-wrap-2 js-fullheight" id="detail-hero">
			<div class="overlay"></div>
			<div class="container">
				<div class="row no-gutters slider-text js-fullheight align-items-end justify-content-center">
					<div class="col-md-9 ftco-animate pb-5 text-center">
						<?php if ($detail): ?>
							<span style="color: black;" class="detail-price" id="detail-price"><?php echo htmlspecialchars($categoryLabel, ENT_QUOTES, 'UTF-8'); ?></span>
							<h1 id="detail-title" class="mb-0"><?php echo htmlspecialchars($detail['nama_wisata'], ENT_QUOTES, 'UTF-8'); ?></h1>
							<?php if (!empty($detail['lokasi_wisata'])): ?>
								<p class="detail-location" id="detail-location"><span class="fa fa-map-marker"></span> <?php echo htmlspecialchars($detail['lokasi_wisata'], ENT_QUOTES, 'UTF-8'); ?></p>
							<?php endif; ?>
						<?php else: ?>
							<span style="color: black;" class="detail-price" id="detail-price">Informasi</span>
							<h1 id="detail-title" class="mb-0">Wisata tidak ditemukan</h1>
							<p class="detail-location" id="detail-location"></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<div class="container py-5">
			<a href="daftar_wisata.php" class="btn btn-secondary mb-4">&larr; Kembali ke Daftar Wisata</a>

			<?php if ($detail): ?>
				<div class="detail-wrap" id="detail-wrap">
					<div class="detail-gallery" id="detail-gallery">
						<div class="thumb" style="background-image: url('<?php echo htmlspecialchars($heroBackground, ENT_QUOTES, 'UTF-8'); ?>');"></div>
						<?php if (count($images) > 1): ?>
							<div class="thumb-list" aria-label="Galeri gambar tambahan">
								<?php foreach (array_slice($images, 1) as $imagePath): ?>
									<img src="<?php echo htmlspecialchars($resolveImagePath($imagePath), ENT_QUOTES, 'UTF-8'); ?>" alt="Foto <?php echo htmlspecialchars($detail['nama_wisata'], ENT_QUOTES, 'UTF-8'); ?>">
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>

					<aside class="detail-sidebar" id="detail-sidebar">
						<div class="price-row">
							<div><strong><?php echo htmlspecialchars($categoryLabel, ENT_QUOTES, 'UTF-8'); ?></strong></div>
							<?php if (!empty($detail['lokasi_wisata'])): ?>
								<div class="text-muted">Lokasi: <?php echo htmlspecialchars($detail['lokasi_wisata'], ENT_QUOTES, 'UTF-8'); ?></div>
							<?php endif; ?>
						</div>

						<h5 class="mt-3">Deskripsi</h5>
						<p><?php echo nl2br(htmlspecialchars($detail['deskripsi'] ?? '-', ENT_QUOTES, 'UTF-8')); ?></p>

						<?php if (!empty($detail['jam_operasi'])): ?>
							<div class="mt-4">
								<h6 class="text-muted mb-1">Jam Operasi</h6>
								<p class="mb-0"><?php echo htmlspecialchars($detail['jam_operasi'], ENT_QUOTES, 'UTF-8'); ?></p>
							</div>
						<?php endif; ?>

						<?php if ($formattedPrice): ?>
							<div class="mt-4">
								<h6 class="text-muted mb-1">Harga Tiket</h6>
								<p class="mb-0"><?php echo htmlspecialchars($formattedPrice, ENT_QUOTES, 'UTF-8'); ?></p>
							</div>
						<?php endif; ?>
					</aside>
				</div>

				<?php if ($mapSrc): ?>
					<div class="detail-map mt-5" id="detail-map" aria-label="Peta lokasi wisata">
						<h5 class="mb-3">Peta Lokasi</h5>
						<div id="map-frame">
							<iframe src="<?php echo htmlspecialchars($mapSrc, ENT_QUOTES, 'UTF-8'); ?>" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
						</div>
					</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php endif; ?>

			<div class="mt-4"><a href="index.html">Home</a></div>
		</div>

	<footer class="ftco-footer bg-bottom ftco-no-pt">
			<div class="container">
				<div class="row mb-5">
					<div class="col-md pt-5">
						<div class="ftco-footer-widget pt-md-5 mb-4">
							<h2 class="ftco-heading-2">About</h2>
							<p>Wisata Kabupaten Bangkalan hadir untuk memperkenalkan pesona alam, budaya, dan kearifan lokal yang dimiliki Bangkalan, Madura.</p>
							<ul class="ftco-footer-social list-unstyled float-md-left float-lft">
								<li class="ftco-animate"><a href="#" title="Twitter"><span class="fa fa-twitter" aria-hidden="true"></span><span class="sr-only">Twitter</span></a></li>
								<li class="ftco-animate"><a href="#" title="Facebook"><span class="fa fa-facebook" aria-hidden="true"></span><span class="sr-only">Facebook</span></a></li>
								<li class="ftco-animate"><a href="#" title="Instagram"><span class="fa fa-instagram" aria-hidden="true"></span><span class="sr-only">Instagram</span></a></li>
							</ul>
						</div>
					</div>
					<div class="col-md pt-5 border-left">
						<div class="ftco-footer-widget pt-md-5 mb-4 ml-md-5">
							<h2 class="ftco-heading-2">Infromation</h2>
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

		<!-- loader -->
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
uery.animateNumber.min.js"></script>
		<script src="js/bootstrap-datepicker.js"></script>
		<script src="js/scrollax.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
		<script src="js/google-map.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>
uery.animateNumber.min.js"></script>
		<script src="js/bootstrap-datepicker.js"></script>
		<script src="js/scrollax.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
		<script src="js/google-map.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>
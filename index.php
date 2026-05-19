<?php
include 'config/koneksi.php';
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MamiraResep</title>

<link rel="stylesheet" href="assets/css/style.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">

<div class="logo">

<img src="assets/images/logo.jpg">

<h2>MamiraResep</h2>

</div>

<ul>

<li><a href="#home">Home</a></li>

<li><a href="#menu">Menu</a></li>

<li><a href="#about">Tentang</a></li>

<li><a href="pages/keranjang.php">Keranjang</a></li>

<?php if(isset($_SESSION['user'])){ ?>

<li>
<a href="auth/logout.php" class="nav-btn">
Logout
</a>
</li>

<?php } else { ?>

<li>
<a href="auth/login.php" class="nav-btn">
Login
</a>
</li>

<?php } ?>

</ul>

</nav>

<!-- HERO -->
<section class="hero" id="home">

<div class="hero-left">

<div class="badge">

✨ Resep Rumahan Premium

</div>

<h1>

Rasa Rumahan,<br>

<span>Kelas Restoran</span>

</h1>

<p>

Nikmati makanan homemade premium dengan resep khas keluarga,
dibuat fresh setiap hari menggunakan bahan terbaik dan penuh cinta.

</p>

<div class="hero-button">

<a href="#menu" class="btn-primary">
Lihat Menu
</a>

<a href="pages/menu.php" class="btn-secondary">
Pesan Sekarang
</a>

</div>

<div class="hero-info">

<div class="info-card">

<h2>1000+</h2>

<p>Pelanggan</p>

</div>

<div class="info-card">

<h2>50+</h2>

<p>Menu Premium</p>

</div>

<div class="info-card">

<h2>4.9★</h2>

<p>Rating</p>

</div>

</div>

</div>

<div class="hero-right">

<img src="assets/images/hero.jpg">

</div>

</section>

<!-- MENU -->
<section class="menu-section" id="menu">

<div class="section-title">

<h1>Menu Favorit</h1>

<p>
Menu pilihan pelanggan terbaik MamiraResep
</p>

</div>

<div class="menu-grid">

<?php

$query = "SELECT * FROM menu_makanan";

$parse = oci_parse($conn,$query);

oci_execute($parse);

while($row = oci_fetch_assoc($parse)){

?>

<div class="menu-card">

<div class="menu-image">

<img src="assets/images/<?php echo $row['GAMBAR']; ?>">

</div>

<div class="menu-content">

<div class="menu-category">

<?php echo $row['KATEGORI']; ?>

</div>

<h2>

<?php echo $row['NAMA_MENU']; ?>

</h2>

<p>

<?php echo $row['DESKRIPSI']; ?>

</p>

<div class="menu-bottom">

<div class="price">

Rp <?php echo number_format($row['HARGA']); ?>

</div>

<a href="tambah_keranjang.php?id=<?php echo $row['ID']; ?>" class="card-btn">

Pesan

</a>

</div>

</div>

</div>

<?php } ?>

</div>

</section>

<!-- ABOUT -->
<section class="about-premium" id="about">

<div class="about-container">

<!-- IMAGE -->

<div class="about-image">

<img src="assets/images/about.jpg" alt="About">

<div class="experience-box">

<h1>10+</h1>
<p>Tahun Pengalaman</p>

</div>

</div>

<!-- TEXT -->

<div class="about-content">

<div class="section-badge">
Tentang Kami
</div>

<h1>
Cita Rasa Rumahan
Dengan Sentuhan Premium
</h1>

<p>
MamiraResep menghadirkan makanan rumahan berkualitas
premium dengan resep keluarga pilihan yang diwariskan
secara turun-temurun.
</p>

<p>
Menggunakan bahan segar, bumbu tradisional,
dan proses memasak terbaik untuk menghadirkan
rasa autentik Nusantara.
</p>

<!-- FEATURES -->

<div class="about-feature">

<div class="feature-card">

<i class="fa-solid fa-utensils"></i>

<div>
<h3>Menu Berkualitas</h3>
<span>Bahan premium pilihan</span>
</div>

</div>

<div class="feature-card">

<i class="fa-solid fa-truck-fast"></i>

<div>
<h3>Fast Delivery</h3>
<span>Pengiriman cepat & aman</span>
</div>

</div>

</div>

<!-- CONTACT -->

<div class="about-contact">

<a href="https://wa.me/628170109029"
target="_blank"
class="contact-item whatsapp">

<div class="icon-circle wa-icon">
<i class="fa-brands fa-whatsapp"></i>
</div>

<div>
<h4>WhatsApp</h4>
<p>Chat Admin</p>
</div>

</a>

<a href="https://instagram.com/mamiraresep"
target="_blank"
class="contact-item instagram">

<div class="icon-circle ig-icon">
<i class="fa-brands fa-instagram"></i>
</div>

<div>
<h4>Instagram</h4>
<p>@mamiraresep</p>
</div>

</a>

<a href="https://maps.google.com/?q=3.585242,98.675598"
target="_blank"
class="contact-item maps-box">

<div class="icon-circle maps-icon">
<i class="fa-solid fa-location-dot"></i>
</div>

<div>
<h4>Google Maps</h4>
<p>Lihat Lokasi</p>
</div>

</a>

</div>

</div>

</div>
</section>
<!-- FOOTER -->
<footer class="footer">

<h2>MamiraResep</h2>

<p>
© 2026 MamiraResep — All Rights Reserved
</p>

</footer>

</body>
</html>
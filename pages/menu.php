<?php

include '../config/koneksi.php';

$query = "SELECT * FROM menu_makanan";

$parse = oci_parse($conn,$query);

oci_execute($parse);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Menu - MamiraResep</title>

<link rel="stylesheet" href="../assets/css/style.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar">

<div class="logo">

<img src="../assets/images/logo.jpg">

<h2>MamiraResep</h2>

</div>

<ul>

<li><a href="../index.php">Home</a></li>

<li><a href="menu.php">Menu</a></li>

<li><a href="keranjang.php">Keranjang</a></li>

</ul>

</nav>

<!-- MENU -->

<section class="menu-section" id="menu">

<div class="section-title">

<h1>Daftar Menu</h1>

<p>Menu spesial pilihan MamiraResep</p>

</div>

<div class="menu-grid">

<?php while($row = oci_fetch_assoc($parse)){ ?>

<div class="menu-card">

<div class="menu-image">

<img src="../assets/images/<?php echo $row['GAMBAR']; ?>">

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

<a href="../tambah_keranjang.php?id=<?php echo $row['ID']; ?>" class="card-btn">

Pesan

</a>

</div>

</div>

</div>

<?php } ?>

</div>

</section>

</body>
</html>
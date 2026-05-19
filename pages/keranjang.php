<?php
session_start();
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Keranjang - MamiraResep</title>

<link rel="stylesheet" href="../assets/css/keranjang.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="cart-container">

<!-- LEFT -->

<div class="cart-left">

<h1 class="cart-title">
Keranjang Belanja
</h1>

<?php

if(isset($_SESSION['keranjang'])){

foreach($_SESSION['keranjang'] as $id => $qty){

$query = "SELECT * FROM menu_makanan WHERE ID='$id'";

$parse = oci_parse($conn,$query);

oci_execute($parse);

$item = oci_fetch_assoc($parse);

$subtotal = $item['HARGA'] * $qty;

?>

<!-- ITEM -->

<div class="cart-item">

<!-- IMAGE -->

<div class="cart-image">

<img src="../assets/images/<?php echo $item['GAMBAR']; ?>">

</div>

<!-- CONTENT -->

<div class="cart-content">

<h2>
<?php echo $item['NAMA_MENU']; ?>
</h2>

<p>
<?php echo $item['DESKRIPSI']; ?>
</p>

<div class="cart-price">

Rp <?php echo number_format($subtotal); ?>

</div>

<!-- QTY -->

<div class="qty-box">

<a href="kurang.php?id=<?php echo $item['ID']; ?>"
class="qty-btn">
-
</a>

<div class="qty-number">

<?php echo $qty; ?>

</div>

<a href="tambah.php?id=<?php echo $item['ID']; ?>"
class="qty-btn">
+
</a>

</div>

</div>

<!-- DELETE -->

<a href="hapus.php?id=<?php echo $item['ID']; ?>"
class="delete-btn">

<i class="fa-solid fa-trash"></i>

</a>

</div>

<?php
}
}
?>

<a href="../index.php#menu" class="back-btn">

<i class="fa-solid fa-arrow-left"></i>

Lanjut Belanja

</a>

</div>

<!-- RIGHT -->

<div class="cart-right">

<h2 class="summary-title">

Ringkasan Belanja

</h2>

<?php

$total = 0;

if(isset($_SESSION['keranjang'])){

foreach($_SESSION['keranjang'] as $id => $qty){

$query = "SELECT * FROM menu_makanan WHERE ID='$id'";

$parse = oci_parse($conn,$query);

oci_execute($parse);

$item = oci_fetch_assoc($parse);

$total += $item['HARGA'] * $qty;

}
}

$ongkir = 5000;

$grandtotal = $total + $ongkir;

?>

<div class="summary-item">

<span>Subtotal</span>

<span>Rp <?php echo number_format($total); ?></span>

</div>

<div class="summary-item">

<span>Ongkir</span>

<span>Rp <?php echo number_format($ongkir); ?></span>

</div>

<div class="summary-total">

<span>Total</span>

<h1>

Rp <?php echo number_format($grandtotal); ?>

</h1>

</div>

<!-- BUTTON -->

<a href="checkout.php" class="checkout-btn">

<i class="fa-solid fa-bag-shopping"></i>

Checkout Sekarang

</a>

<a href="https://wa.me/6281234567890"
target="_blank"
class="wa-btn">

<i class="fa-brands fa-whatsapp"></i>

Chat Admin

</a>

<!-- INFO -->

<div class="info-box">

<div class="info-card">

<i class="fa-solid fa-shield-halved"></i>

<div>

<h4>Pembayaran Aman</h4>

<p>Transaksi terpercaya</p>

</div>

</div>

<div class="info-card">

<i class="fa-solid fa-truck-fast"></i>

<div>

<h4>Pengiriman Cepat</h4>

<p>Proses cepat & aman</p>

</div>

</div>

<div class="info-card">

<i class="fa-solid fa-star"></i>

<div>

<h4>Kualitas Premium</h4>

<p>Bahan fresh setiap hari</p>

</div>

</div>

</div>

</div>

</div>

</body>
</html>
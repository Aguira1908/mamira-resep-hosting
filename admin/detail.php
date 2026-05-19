<?php
include '../config/koneksi.php';

$id = $_GET['id'];

/* DATA PESANAN */
$q = "SELECT * FROM pesanan WHERE id='$id'";
$p = oci_parse($conn,$q);
oci_execute($p);
$data = oci_fetch_assoc($p);

/* DETAIL MENU */
$detail = "SELECT d.qty, d.subtotal, m.nama_menu
FROM detail_pesanan d
JOIN menu_makanan m ON m.id = d.menu_id
WHERE d.pesanan_id='$id'";

$dq = oci_parse($conn,$detail);
oci_execute($dq);
?>

<!DOCTYPE html>
<html>
<head>
<title>Detail Pesanan</title>

<style>
body{font-family:Arial;padding:30px;background:#f3f3f3;}
.box{background:white;padding:25px;border-radius:12px;}
h2{color:#d9a441;}
</style>

</head>
<body>

<div class="box">

<h2>Detail Pesanan #<?= $id ?></h2>

<p>Nama: <?= $data['NAMA'] ?></p>
<p>WA: <?= $data['WA'] ?></p>
<p>Alamat: <?= $data['ALAMAT'] ?></p>
<p>Status: <?= $data['STATUS'] ?></p>

<hr>

<h3>Menu Pesanan</h3>

<?php while($row = oci_fetch_assoc($dq)) { ?>

<p>
<?= $row['NAMA_MENU'] ?> |
Qty: <?= $row['QTY'] ?> |
Subtotal: Rp <?= number_format($row['SUBTOTAL']) ?>
</p>

<?php } ?>

<h3>Total: Rp <?= number_format($data['TOTAL']) ?></h3>

</div>

</body>
</html>
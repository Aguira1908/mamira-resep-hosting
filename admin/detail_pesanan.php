<?php
include '../config/koneksi.php';

session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
}

if($_SESSION['user']['ROLE'] != "admin"){
    header("Location: ../index.php");
}

$id = $_GET['id'];

/* DATA PESANAN */

$query = "
SELECT *
FROM pesanan
WHERE id='$id'
";

$parse = oci_parse($conn,$query);

oci_execute($parse);

$data = oci_fetch_assoc($parse);

/* DETAIL PESANAN */

$queryDetail = "
SELECT
    d.*,
    m.nama_menu,
    m.gambar
FROM detail_pesanan d
LEFT JOIN menu_makanan m
ON d.menu_id = m.id
WHERE d.pesanan_id = '$id'
";

$dparse = oci_parse($conn,$queryDetail);

oci_execute($dparse);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Detail Pesanan</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

.detail-wrapper{
display:grid;
gap:30px;
}

.detail-card{
background:#161616;
padding:40px;
border-radius:28px;
}

.detail-card h2{
color:#d9a441;
margin-bottom:30px;
font-size:28px;
}

.detail-item{
margin-bottom:25px;
}

.detail-item h4{
color:#888;
margin-bottom:8px;
font-size:14px;
}

.detail-item p{
font-size:20px;
font-weight:600;
}

.status-badge{
display:inline-block;
padding:12px 20px;
border-radius:14px;
background:#d9a441;
color:black;
font-weight:700;
}

.table-box{
background:#161616;
padding:40px;
border-radius:28px;
}

.table-box h2{
color:#d9a441;
margin-bottom:30px;
}

table{
width:100%;
border-collapse:collapse;
}

table th{
background:#d9a441;
color:black;
padding:18px;
text-align:left;
}

table td{
padding:18px;
border-bottom:1px solid rgba(255,255,255,0.05);
}

.table-img{
width:85px;
height:85px;
object-fit:cover;
border-radius:18px;
}

.no-image{
width:85px;
height:85px;
border-radius:18px;
background:#222;
display:flex;
align-items:center;
justify-content:center;
font-size:12px;
color:#777;
}

.back-btn{
display:inline-block;
margin-top:30px;
padding:16px 30px;
background:#d9a441;
color:black;
text-decoration:none;
border-radius:16px;
font-weight:700;
}

</style>

</head>

<body>

<div class="sidebar">

<div class="sidebar-logo">

<img src="../assets/images/logo.jpg">

<h2>MamiraResep</h2>

</div>

<ul>

<li>
<a href="dashboard.php">
<i class="fa-solid fa-house"></i>
<span>Dashboard</span>
</a>
</li>

<li>
<a href="menu.php">
<i class="fa-solid fa-bowl-food"></i>
<span>Kelola Menu</span>
</a>
</li>

<li>
<a href="pesanan.php" class="active">
<i class="fa-solid fa-cart-shopping"></i>
<span>Pesanan</span>
</a>
</li>

<li>
<a href="../auth/logout.php">
<i class="fa-solid fa-right-from-bracket"></i>
<span>Logout</span>
</a>
</li>

</ul>

</div>

<div class="main-content">

<div class="topbar">

<h1>Detail Pesanan</h1>

</div>

<div class="detail-wrapper">

<div class="detail-card">

<h2>Informasi Pemesan</h2>

<div class="detail-item">
<h4>Nama User</h4>
<p><?php echo $data['NAMA']; ?></p>
</div>

<div class="detail-item">
<h4>Total Pesanan</h4>
<p>Rp <?php echo number_format($data['TOTAL']); ?></p>
</div>

<div class="detail-item">
<h4>Status</h4>

<div class="status-badge">
<?php echo $data['STATUS']; ?>
</div>

</div>

<div class="detail-item">
<h4>Metode Pembayaran</h4>
<p><?php echo $data['PEMBAYARAN']; ?></p>
</div>

<div class="detail-item">
<h4>Alamat</h4>
<p><?php echo $data['ALAMAT']; ?></p>
</div>

<div class="detail-item">
<h4>Tanggal Pesan</h4>
<p><?php echo $data['TANGGAL_PESAN']; ?></p>
</div>

</div>

<div class="table-box">

<h2>Menu Yang Dipesan</h2>

<table>

<tr>

<th>Gambar</th>

<th>Menu</th>

<th>Qty</th>

<th>Subtotal</th>

</tr>

<?php while($d = oci_fetch_assoc($dparse)){ ?>

<tr>

<td>

<?php if($d['GAMBAR'] != ""){ ?>

<img
src="../assets/images/<?php echo $d['GAMBAR']; ?>"
class="table-img"
>

<?php } else { ?>

<div class="no-image">
No Image
</div>

<?php } ?>

</td>

<td>

<?php
echo $d['NAMA_MENU']
?? 'Menu sudah dihapus';
?>

</td>

<td>

<?php echo $d['QTY']; ?>

</td>

<td>

Rp <?php echo number_format($d['SUBTOTAL']); ?>

</td>

</tr>

<?php } ?>

</table>

<a href="pesanan.php" class="back-btn">

Kembali

</a>

</div>

</div>

</div>

</body>
</html>
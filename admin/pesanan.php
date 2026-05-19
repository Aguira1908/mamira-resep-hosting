<?php
include '../config/koneksi.php';

session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
}

if($_SESSION['user']['ROLE'] != "admin"){
    header("Location: ../index.php");
}

/* UPDATE STATUS */

if(isset($_POST['update_status'])){

    $id = $_POST['id'];
    $status = $_POST['status'];

    $update = "
    UPDATE pesanan
    SET status='$status'
    WHERE id='$id'
    ";

    $uparse = oci_parse($conn,$update);
    oci_execute($uparse);
}

/* AMBIL DATA PESANAN */

$query = "
SELECT *
FROM pesanan
ORDER BY id DESC
";

$parse = oci_parse($conn,$query);

oci_execute($parse);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Data Pesanan</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

.order-wrapper{
    margin-top:30px;
}

.order-card{
    background:#161616;
    border-radius:24px;
    padding:25px;
    margin-bottom:25px;
    border:1px solid rgba(255,255,255,0.05);
    transition:0.3s;
}

.order-card:hover{
    transform:translateY(-5px);
}

.order-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.order-user{
    font-size:22px;
    font-weight:700;
}

.order-date{
    color:#999;
    font-size:14px;
}

.order-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
    margin-top:15px;
}

.order-box{
    background:#1d1d1d;
    padding:18px;
    border-radius:18px;
}

.order-box h4{
    color:#999;
    margin-bottom:8px;
    font-size:14px;
}

.order-box p{
    font-size:20px;
    font-weight:600;
}

.status-form{
    display:flex;
    gap:12px;
    align-items:center;
    margin-top:25px;
}

.status-select{
    padding:12px 15px;
    border:none;
    border-radius:12px;
    background:#222;
    color:white;
    font-size:14px;
}

.save-btn{
    padding:12px 18px;
    border:none;
    border-radius:12px;
    background:#d9a441;
    color:black;
    font-weight:700;
    cursor:pointer;
}

.detail-btn{
    padding:12px 20px;
    border-radius:12px;
    background:#3d7bfd;
    color:white;
    text-decoration:none;
    font-weight:600;
}

.badge{
    display:inline-block;
    padding:10px 16px;
    border-radius:12px;
    font-size:14px;
    font-weight:700;
    margin-top:10px;
}

.proses{
    background:#ffc107;
    color:black;
}

.selesai{
    background:#28a745;
    color:white;
}

.dikirim{
    background:#17a2b8;
    color:white;
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
<h1>Data Pesanan</h1>
</div>

<div class="order-wrapper">

<?php while($data = oci_fetch_assoc($parse)){ ?>

<div class="order-card">

<div class="order-top">

<div>
<div class="order-user">
<?php echo $data['NAMA']; ?>
</div>

<div class="order-date">
<?php echo $data['TANGGAL_PESAN']; ?>
</div>
</div>

<a href="detail_pesanan.php?id=<?php echo $data['ID']; ?>" class="detail-btn">
Detail
</a>

</div>

<div class="order-grid">

<div class="order-box">
<h4>Total</h4>
<p>Rp <?php echo number_format($data['TOTAL']); ?></p>
</div>

<div class="order-box">
<h4>Pembayaran</h4>
<p><?php echo $data['PEMBAYARAN']; ?></p>
</div>

<div class="order-box">
<h4>WhatsApp</h4>
<p><?php echo $data['WA']; ?></p>
</div>

<div class="order-box">
<h4>Status</h4>

<?php
$statusClass = "proses";

if($data['STATUS'] == "Selesai"){
    $statusClass = "selesai";
}

if($data['STATUS'] == "Dikirim"){
    $statusClass = "dikirim";
}
?>

<div class="badge <?php echo $statusClass; ?>">
<?php echo $data['STATUS']; ?>
</div>

</div>

</div>

<form method="POST" class="status-form">

<input type="hidden" name="id"
value="<?php echo $data['ID']; ?>">

<select name="status" class="status-select">

<option value="Diproses"
<?php if($data['STATUS']=="Diproses") echo "selected"; ?>>
Diproses
</option>

<option value="Dikirim"
<?php if($data['STATUS']=="Dikirim") echo "selected"; ?>>
Dikirim
</option>

<option value="Selesai"
<?php if($data['STATUS']=="Selesai") echo "selected"; ?>>
Selesai
</option>

</select>

<button type="submit"
name="update_status"
class="save-btn">

Update Status

</button>

</form>

</div>

<?php } ?>

</div>

</div>

</body>
</html>
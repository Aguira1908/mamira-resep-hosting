<?php
include '../config/koneksi.php';

session_start();

if(!isset($_SESSION['user'])){

header("Location: ../auth/login.php");

}

if($_SESSION['user']['ROLE'] != "admin"){

header("Location: ../index.php");

}

$q1 = oci_parse($conn,"
SELECT COUNT(*) AS TOTAL
FROM menu_makanan
");

oci_execute($q1);

$m1 = oci_fetch_assoc($q1);

$q2 = oci_parse($conn,"
SELECT COUNT(*) AS TOTAL
FROM users
");

oci_execute($q2);

$m2 = oci_fetch_assoc($q2);

$q3 = oci_parse($conn,"
SELECT COUNT(*) AS TOTAL
FROM pesanan
");

oci_execute($q3);

$m3 = oci_fetch_assoc($q3);
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="sidebar">

<div class="sidebar-logo">

<img src="../assets/images/logo.jpg">

<h2>MamiraResep</h2>

</div>

<ul>

<li>

<a href="dashboard.php" class="active">

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

<a href="#">

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

<h1>Dashboard Admin</h1>

</div>

<div class="dashboard-grid">

<div class="dashboard-card">

<div class="card-icon gold">

<i class="fa-solid fa-bowl-food"></i>

</div>

<div>

<h2>
<?php echo $m1['TOTAL']; ?>
</h2>

<p>Total Menu</p>

</div>

</div>

<div class="dashboard-card">

<div class="card-icon blue">

<i class="fa-solid fa-users"></i>

</div>

<div>

<h2>
<?php echo $m2['TOTAL']; ?>
</h2>

<p>Total User</p>

</div>

</div>

<div class="dashboard-card">

<div class="card-icon red">

<i class="fa-solid fa-cart-shopping"></i>

</div>

<div>

<h2>
<?php echo $m3['TOTAL']; ?>
</h2>

<p>Total Pesanan</p>

</div>

</div>

</div>

</div>

</body>
</html>
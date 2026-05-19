<?php
include '../config/koneksi.php';

session_start();

$query = oci_parse($conn,"
SELECT * FROM menu_makanan
ORDER BY id DESC
");

oci_execute($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Kelola Menu</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
<a href="menu.php" class="active">
<i class="fa-solid fa-bowl-food"></i>
<span>Kelola Menu</span>
</a>
</li>

<li>
<a href="pesanan.php">
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

<h1>Kelola Menu</h1>

</div>

<div class="table-box">

<table>

<tr>

<th>Gambar</th>
<th>Nama Menu</th>
<th>Kategori</th>
<th>Harga</th>
<th>Aksi</th>

</tr>

<?php while($row = oci_fetch_assoc($query)){ ?>

<tr>

<td>
<img src="../assets/images/<?php echo trim($row['GAMBAR']); ?>" class="table-img">
</td>

<td>
<?php echo $row['NAMA_MENU']; ?>
</td>

<td>
<?php echo $row['KATEGORI']; ?>
</td>

<td>
Rp <?php echo number_format($row['HARGA']); ?>
</td>

<td>

<div class="action-button">

<a href="edit_menu.php?id=<?php echo $row['ID']; ?>" class="edit-btn">
Edit
</a>

<a href="hapus_menu.php?id=<?php echo $row['ID']; ?>" class="hapus-btn">
Hapus
</a>

</div>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>
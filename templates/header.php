<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MamiraResep</title>

<link rel="stylesheet" href="/MAMIRARESEP/assets/css/style.css">

</head>

<body>

<header>

<nav>

<div class="logo">
<span>MamiraResep</span>
</div>

<ul>

<li><a href="/MAMIRARESEP/index.php">Home</a></li>
<li><a href="/MAMIRARESEP/pages/menu.php">Menu</a></li>
<li><a href="/MAMIRARESEP/pages/keranjang.php">Keranjang</a></li>

<?php if(isset($_SESSION['user'])){ ?>

<li>
<a href="/MAMIRARESEP/auth/logout.php">
Logout
</a>
</li>

<?php } else { ?>

<li>
<a href="/MAMIRARESEP/auth/login.php">
Login
</a>
</li>

<?php } ?>

</ul>

</nav>

</header>
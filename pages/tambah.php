<?php
session_start();

$id = $_GET['id'];

$_SESSION['keranjang'][$id]++;

header("Location: keranjang.php");
?>
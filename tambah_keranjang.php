<?php
session_start();

include 'config/koneksi.php';

$id = $_GET['id'];

if(!isset($_SESSION['keranjang'])){
    $_SESSION['keranjang'] = [];
}

if(isset($_SESSION['keranjang'][$id])){
    $_SESSION['keranjang'][$id] += 1;
}else{
    $_SESSION['keranjang'][$id] = 1;
}

header("Location: pages/keranjang.php");
exit;
?>
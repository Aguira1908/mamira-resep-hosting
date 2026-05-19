<?php
include '../config/koneksi.php';

$id = $_POST['id'];

$nama = $_POST['nama_menu'];

$kategori = $_POST['kategori'];

$harga = $_POST['harga'];

$gambar = $_POST['gambar'];

$deskripsi = $_POST['deskripsi'];

$query = oci_parse($conn,"
UPDATE menu_makanan
SET

nama_menu='$nama',
kategori='$kategori',
harga='$harga',
gambar='$gambar',
deskripsi='$deskripsi'

WHERE id='$id'
");

oci_execute($query);

header("Location: menu.php");
?>
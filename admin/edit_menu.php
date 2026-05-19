<?php
include '../config/koneksi.php';

$id = $_GET['id'];

$query = oci_parse($conn,"
SELECT * FROM menu_makanan
WHERE id='$id'
");

oci_execute($query);

$data = oci_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<title>Edit Menu</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>

<div class="main-content">

<h1 style="margin-bottom:30px;">Edit Menu</h1>

<form action="update_menu.php" method="POST" class="form-box">

<input type="hidden" name="id"
value="<?php echo $data['ID']; ?>">

<div class="input-flex">

<input type="text"
name="nama_menu"
value="<?php echo $data['NAMA_MENU']; ?>"
required>

<select name="kategori">

<option value="Catering">Catering</option>

<option value="Snack">Snack</option>

</select>

</div>

<div class="input-flex">

<input type="number"
name="harga"
value="<?php echo $data['HARGA']; ?>"
required>

<input type="text"
name="gambar"
value="<?php echo $data['GAMBAR']; ?>"
required>

</div>

<textarea name="deskripsi"><?php echo $data['DESKRIPSI']; ?></textarea>

<button type="submit">
Update Menu
</button>

</form>

</div>

</body>
</html>
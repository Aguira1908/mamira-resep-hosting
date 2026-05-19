<?php
session_start();

include '../config/koneksi.php';

$user_id = 1;

$nama = $_POST['nama'];
$wa = $_POST['wa'];
$alamat = $_POST['alamat'];
$pembayaran = $_POST['pembayaran'];
$total = $_POST['total'];

$tanggal = date('Y-m-d');

$bukti = "";

/* UPLOAD BUKTI */

if(isset($_FILES['bukti']['name'])){

$namaFile =
time().'_'.$_FILES['bukti']['name'];

$tmp =
$_FILES['bukti']['tmp_name'];

move_uploaded_file(
$tmp,
"../assets/bukti/".$namaFile
);

$bukti = $namaFile;

}

/* STATUS */

if($pembayaran == "COD"){

$status = "Pesanan Diproses";

}else{

if($bukti != ""){

$status = "Pesanan Diproses";

}else{

$status = "Menunggu Pembayaran";

}

}

/* =========================
   DETAIL PESANAN
========================= */

$detail_pesanan = "";

if(isset($_SESSION['keranjang'])){

foreach($_SESSION['keranjang'] as $id => $qty){

$q =
"SELECT * FROM menu_makanan
WHERE id='$id'";

$p =
oci_parse($conn,$q);

oci_execute($p);

$data =
oci_fetch_assoc($p);

$nama_menu =
$data['NAMA_MENU'];

$harga =
$data['HARGA'];

$subtotal =
$harga * $qty;

$detail_pesanan .=

"• ".$nama_menu."

Qty : ".$qty."

Subtotal : Rp ".
number_format($subtotal)."

-------------------------

";

}

}
/* UPLOAD BUKTI */

$bukti = "";

if(isset($_FILES['bukti']) && $_FILES['bukti']['name'] != ""){

    $namaFile = $_FILES['bukti']['name'];

    $tmp = $_FILES['bukti']['tmp_name'];

    $bukti = time().'_'.$namaFile;

    move_uploaded_file(
        $tmp,
        "uploads/".$bukti
    );
}
/* =========================
   INSERT DATABASE
========================= */

$query = "INSERT INTO pesanan
(
user_id,
total,
tanggal_pesan,
status,
nama,
wa,
alamat,
pembayaran,
bukti_pembayaran
)

VALUES
(
'$user_id',
'$total',
TO_DATE('$tanggal','YYYY-MM-DD'),
'$status',
'$nama',
'$wa',
'$alamat',
'$pembayaran',
'$bukti'
)";

$parse =
oci_parse($conn,$query);

oci_execute($parse);
/* =========================
   AMBIL ID PESANAN
========================= */

$idQuery =
"SELECT MAX(id) as ID FROM pesanan";

$idParse =
oci_parse($conn,$idQuery);

oci_execute($idParse);

$idData =
oci_fetch_assoc($idParse);

$pesanan_id =
$idData['ID'];

/* =========================
   INSERT DETAIL PESANAN
========================= */

if(isset($_SESSION['keranjang'])){

foreach($_SESSION['keranjang'] as $id => $qty){

$q =
"SELECT * FROM menu_makanan
WHERE id='$id'";

$p =
oci_parse($conn,$q);

oci_execute($p);

$data =
oci_fetch_assoc($p);

$harga =
$data['HARGA'];

$subtotal =
$harga * $qty;

$insertDetail =
"INSERT INTO detail_pesanan
(
pesanan_id,
menu_id,
qty,
subtotal
)

VALUES
(
'$pesanan_id',
'$id',
'$qty',
'$subtotal'
)";

$dparse =
oci_parse($conn,$insertDetail);

oci_execute($dparse);

}

}

/* HAPUS KERANJANG */

unset($_SESSION['keranjang']);
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Invoice</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{

background:#f3f3f3;

padding:40px;
}

.invoice{

max-width:800px;

margin:auto;

background:white;

padding:45px;

border-radius:25px;

border:3px solid #d9a441;

box-shadow:
0 10px 30px rgba(0,0,0,0.08);
}

.logo{

font-size:45px;

font-weight:bold;

color:#d9a441;

margin-bottom:20px;
}

.title{

font-size:28px;

margin-bottom:25px;
}

p{

font-size:18px;

margin:18px 0;

line-height:1.8;
}

.total{

font-size:34px;

font-weight:bold;

margin-top:35px;

color:#d9a441;
}

.footer{

margin-top:50px;

color:#777;

font-size:16px;
}

.status{

display:inline-block;

padding:10px 18px;

border-radius:12px;

background:#d9a441;

color:white;

margin-top:10px;

font-weight:600;
}

.detail-box{

margin-top:35px;

padding:25px;

background:#f8f8f8;

border-radius:18px;

line-height:1.9;
}

.detail-box h2{

margin-bottom:20px;

color:#d9a441;
}

pre{

white-space:pre-wrap;

font-size:16px;
}

</style>

</head>

<body>

<div class="invoice"
id="invoice">

<div class="logo">
MamiraResep
</div>

<hr><br>

<h2 class="title">
Invoice Pemesanan
</h2>

<p>

<b>Nama :</b>

<?php echo $nama; ?>

</p>

<p>

<b>No WhatsApp :</b>

<?php echo $wa; ?>

</p>

<p>

<b>Alamat :</b>

<?php echo $alamat; ?>

</p>

<p>

<b>Metode Pembayaran :</b>

<?php echo $pembayaran; ?>

</p>

<p>

<b>Status Pesanan :</b>

</p>

<div class="status">

<?php echo $status; ?>

</div>

<!-- DETAIL PESANAN -->

<div class="detail-box">

<h2>
Detail Pesanan
</h2>

<pre>

<?php echo $detail_pesanan; ?>

</pre>

</div>

<div class="total">

Total Pembayaran :
Rp <?php echo number_format($total); ?>

</div>

<div class="footer">

Terima kasih telah memesan
di MamiraResep 🍱

</div>

</div>

<script>

window.onload = function(){

const element =
document.getElementById("invoice");

/* DOWNLOAD PDF */

html2pdf()

.from(element)

.save("Invoice-MamiraResep.pdf");

/* WHATSAPP ADMIN */

setTimeout(function(){

const admin =
"6289614408366";

const pesan =
`Halo Admin MamiraResep 🍱

Ada pesanan baru.

====================

Nama :
<?php echo $nama; ?>

No WhatsApp :
<?php echo $wa; ?>

Alamat :
<?php echo $alamat; ?>

Pembayaran :
<?php echo $pembayaran; ?>

Status :
<?php echo $status; ?>

====================

DETAIL PESANAN :

<?php echo $detail_pesanan; ?>

====================

TOTAL :
Rp <?php echo number_format($total); ?>

`;

window.location.href =

`https://wa.me/${admin}?text=${encodeURIComponent(pesan)}`;

},2000);

/* BALIK HOME */

setTimeout(function(){

window.location =
"../index.php";

},5000);

}

</script>

</body>
</html>
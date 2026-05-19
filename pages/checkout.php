<?php
session_start();
include '../config/koneksi.php';

$total = 0;

if(isset($_SESSION['keranjang'])){

foreach($_SESSION['keranjang'] as $id => $qty){

$query =
"SELECT * FROM menu_makanan WHERE ID='$id'";

$parse = oci_parse($conn,$query);

oci_execute($parse);

$item = oci_fetch_assoc($parse);

$total += $item['HARGA'] * $qty;

}
}

$ongkir = 5000;

$grandtotal = $total + $ongkir;
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Checkout</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins;
}

body{
background:#0f0f0f;
padding:50px 8%;
color:white;
}

.checkout-container{

display:grid;

grid-template-columns:1.3fr 0.7fr;

gap:35px;
}

.checkout-left,
.checkout-right{

background:#171717;

padding:35px;

border-radius:30px;
}

.checkout-title{

font-size:40px;

color:#d9a441;

margin-bottom:30px;
}

.form-group{

margin-bottom:25px;
}

.form-group label{

display:block;

margin-bottom:10px;
}

.form-group input,
.form-group textarea,
.form-group select{

width:100%;

padding:18px;

border:none;

border-radius:18px;

background:#232323;

color:white;

font-size:16px;
}

textarea{
height:120px;
resize:none;
}

.payment-box{

background:#232323;

padding:25px;

border-radius:20px;

margin-top:20px;
}

.rekening-item{

background:#2d2d2d;

padding:18px;

border-radius:15px;

margin-bottom:15px;
}

.rekening-item b{

color:#d9a441;
}

.rekening-item p{

font-size:20px;

margin:8px 0;
}

.qris-img{

width:250px;

margin-top:20px;

border-radius:20px;
}

.summary-title{

font-size:30px;

margin-bottom:30px;

color:#d9a441;
}

.summary-item{

display:flex;

justify-content:space-between;

margin-bottom:18px;
}

.summary-total{

display:flex;

justify-content:space-between;

margin-top:30px;

padding-top:25px;

border-top:1px solid #333;
}

.summary-total h1{

font-size:35px;

color:#d9a441;
}

.checkout-btn{

width:100%;

padding:18px;

border:none;

border-radius:18px;

background:#d9a441;

color:white;

font-size:18px;

cursor:pointer;

margin-top:30px;
}

.checkout-btn:hover{

background:#efb757;
}

</style>

</head>

<body>

<div class="checkout-container">

<!-- LEFT -->

<div class="checkout-left">

<h1 class="checkout-title">
Checkout
</h1>

<form action="invoice.php"
method="POST"
enctype="multipart/form-data">

<div class="form-group">

<label>Nama Lengkap</label>

<input type="text"
name="nama"
required>

</div>

<div class="form-group">

<label>No WhatsApp</label>

<input type="text"
name="wa"
required>

</div>

<div class="form-group">

<label>Alamat Lengkap</label>

<textarea
name="alamat"
required></textarea>

</div>

<div class="form-group">

<label>Metode Pembayaran</label>

<select
name="pembayaran"
id="pembayaran"
onchange="showPayment()"
required>

<option value="">
Pilih Pembayaran
</option>

<option value="Transfer Bank">
Transfer Bank
</option>

<option value="COD">
COD
</option>

<option value="QRIS">
QRIS
</option>

</select>

</div>

<!-- BANK -->

<div id="bankBox"
style="display:none;">

<div class="payment-box">

<h3>Transfer Bank</h3>

<div class="rekening-item">

<b>BCA</b>

<p>8075237819</p>

<span>a/n Devira Natawijaya</span>

</div>

<div class="rekening-item">

<b>Mandiri</b>

<p>1050019833189</p>

<span>a/n Devira Natawijaya</span>

</div>

<div class="rekening-item">

<b>BRI</b>

<p>3383010310205339</p>

<span>a/n Devira Natawijaya</span>

</div>

</div>

</div>

<!-- QRIS -->

<div id="qrisBox"
style="display:none;">

<div class="payment-box">

<h3>Scan QRIS</h3>

<img src="../assets/images/qris.png"
class="qris-img">

</div>

</div>

<!-- UPLOAD -->

<div class="form-group"
id="uploadBox"
style="display:none;">

<label>Upload Bukti Pembayaran</label>

<input type="file"
name="bukti"
accept="image/*">

</div>

<input type="hidden"
name="total"
value="<?php echo $grandtotal; ?>">

<button class="checkout-btn">

<i class="fa-solid fa-bag-shopping"></i>

Pesan Sekarang

</button>

</form>

</div>

<!-- RIGHT -->

<div class="checkout-right">

<h2 class="summary-title">
Ringkasan
</h2>

<div class="summary-item">

<span>Subtotal</span>

<span>
Rp <?php echo number_format($total); ?>
</span>

</div>

<div class="summary-item">

<span>Ongkir</span>

<span>
Rp <?php echo number_format($ongkir); ?>
</span>

</div>

<div class="summary-total">

<span>Total</span>

<h1>
Rp <?php echo number_format($grandtotal); ?>
</h1>

</div>

</div>

</div>

<script>

function showPayment(){

const metode =
document.getElementById("pembayaran").value;

const bankBox =
document.getElementById("bankBox");

const qrisBox =
document.getElementById("qrisBox");

const uploadBox =
document.getElementById("uploadBox");

bankBox.style.display = "none";
qrisBox.style.display = "none";
uploadBox.style.display = "none";

if(metode == "Transfer Bank"){

bankBox.style.display = "block";
uploadBox.style.display = "block";

}

else if(metode == "QRIS"){

qrisBox.style.display = "block";
uploadBox.style.display = "block";

}

}

</script>

</body>
</html>
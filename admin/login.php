<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['login'])){

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM admin 
WHERE username='$username' 
AND password='$password'";

$parse = oci_parse($conn,$query);
oci_execute($parse);

$data = oci_fetch_assoc($parse);

if($data){

$_SESSION['admin'] = $data['USERNAME'];

header("Location: pesanan.php");
exit();

}else{

echo "<script>alert('Login gagal!');</script>";

}

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login Admin</title>

<style>
body{
font-family:Arial;
background:#f3f3f3;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
}

.box{
background:white;
padding:30px;
border-radius:12px;
width:300px;
box-shadow:0 10px 20px rgba(0,0,0,0.1);
}

input{
width:100%;
padding:10px;
margin:10px 0;
}

button{
width:100%;
padding:10px;
background:#d9a441;
color:white;
border:none;
cursor:pointer;
}
</style>
</head>

<body>

<div class="box">

<h2>Admin Login</h2>

<form method="POST">

<input type="text" name="username" placeholder="Username" required>

<input type="password" name="password" placeholder="Password" required>

<button name="login">Login</button>

</form>

</div>

</body>
</html>
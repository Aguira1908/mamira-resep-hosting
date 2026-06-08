<?php
include '../config/koneksi.php';

session_start();

if(isset($_POST['login'])){

$email = $_POST['email'];

$password = $_POST['password'];

$query = "
SELECT *
FROM users
WHERE email = :email
";

$parse = oci_parse($conn,$query);

oci_bind_by_name($parse,":email",$email);

oci_execute($parse);

$data = oci_fetch_assoc($parse);

if($data && $password == $data['PASSWORD']){

$_SESSION['user'] = $data;

/* ROLE */

if($data['ROLE'] == "admin"){

header("Location: ../admin/dashboard.php");

}else{

header("Location: ../index.php");

}

}else{

$error = "Email atau Password Salah";

}

}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login MamiraResep</title>

<link rel="stylesheet" href="../assets/css/login.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

</head>

<body>

<div class="login-container">

<div class="login-box">

<h1>MamiraResep</h1>

<p>
Login terlebih dahulu
</p>

<?php if(isset($error)){ ?>

<div class="error">

<?php echo $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="input-group">

<input
type="email"
name="email"
placeholder="Email"
required
>

</div>

<div class="input-group">

<input
type="password"
name="password"
placeholder="Password"
required
>

</div>

<button type="submit" name="login">

Login

</button>

</form>

<div class="register-link">

Belum punya akun?

<a href="register.php">
Daftar
</a>

</div>

</div>

</div>

</body>
</html>
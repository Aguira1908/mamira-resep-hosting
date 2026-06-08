<?php
session_start();

include '../config/koneksi.php';

$message = "";

if (isset($_POST['register'])) {

  $nama = $_POST['nama'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  $cek = "SELECT * FROM users WHERE email='$email'";

  $parse = oci_parse($conn, $cek);

  oci_execute($parse);

  if (oci_fetch_assoc($parse)) {

    $message = "
        <div class='alert alert-error'>
        Email sudah digunakan
        </div>";
  } else {

    $query = "
        INSERT INTO users
        (nama,email,password,role)
        VALUES
        ('$nama','$email','$password','user')
        ";

    $insert = oci_parse($conn, $query);

    if (oci_execute($insert)) {

      echo "
    <script>

    alert('Registrasi berhasil');

    window.location='login.php';

    </script>
    ";

      exit;
    } else {

      $message = "
            <div class='alert alert-error'>
            Registrasi gagal
            </div>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

  <meta charset="UTF-8">

  <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

  <title>Register - MamiraResep</title>

  <link rel="stylesheet"
    href="../assets/css/register.css">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

</head>

<body>

  <div class="register-box">

    <h1>MamiraResep</h1>

    <p>
      Daftar akun baru terlebih dahulu
    </p>

    <?php echo $message; ?>

    <form method="POST">

      <div class="input-group">

        <input
          type="text"
          name="nama"
          placeholder="Nama Lengkap"
          required>

      </div>

      <div class="input-group">

        <input
          type="email"
          name="email"
          placeholder="Email"
          required>

      </div>

      <div class="input-group">

        <input
          type="password"
          name="password"
          placeholder="Password"
          required>

      </div>

      <button
        type="submit"
        name="register"
        class="register-btn">

        Daftar

      </button>

    </form>

    <div class="login-link">

      Sudah punya akun?

      <a href="login.php">
        Login
      </a>

    </div>

  </div>

</body>

</html>
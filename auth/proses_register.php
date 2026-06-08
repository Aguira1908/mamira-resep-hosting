<?php

include '../config/koneksi.php';

$nama = $_POST['nama'];
$email = $_POST['email'];

$password = $_POST['password'];

$role = "user";

$query = "INSERT INTO users
(nama,email,password,role)
VALUES
(:nama,:email,:password,:role)";

$parse = oci_parse($conn,$query);

oci_bind_by_name($parse,":nama",$nama);
oci_bind_by_name($parse,":email",$email);
oci_bind_by_name($parse,":password",$password);
oci_bind_by_name($parse,":role",$role);

oci_execute($parse);

header("Location: login.php");
?>
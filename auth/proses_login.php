<?php

session_start();

include '../config/koneksi.php';

$email = $_POST['email'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE email = :email";

$parse = oci_parse($conn,$query);

oci_bind_by_name($parse,":email",$email);

oci_execute($parse);

$data = oci_fetch_assoc($parse);

if($data){

    if(password_verify($password,$data['PASSWORD'])){

        $_SESSION['user'] = $data;

        if($data['ROLE'] == "admin"){

            header("Location: ../admin/dashboard.php");

        }else{

            header("Location: ../index.php");
        }

    }else{

        echo "Password salah";
    }

}else{

    echo "Email tidak ditemukan";
}
?>
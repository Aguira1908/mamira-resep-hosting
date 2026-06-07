<?php

// Kredensial dari VPS
$username = "mamira_resep";
$password = "PassMamira123";
$host = "127.0.0.2";
$port = "1521";
$service = "xepdb1";

// Format connection string Oracle: host:port/service
$tns = $host . ':' . $port . '/' . $service;

$conn = oci_connect($username, $password, $tns);

if (!$conn) {
  $e = oci_error();
  echo "Koneksi gagal: " . htmlentities($e['message'], ENT_QUOTES);
}

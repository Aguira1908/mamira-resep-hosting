<?php
include '../config/koneksi.php';

$id = $_POST['id'];

$status = $_POST['status'];

$query = oci_parse($conn,"
UPDATE pesanan
SET status=:status
WHERE id=:id
");

oci_bind_by_name($query,":status",$status);

oci_bind_by_name($query,":id",$id);

oci_execute($query, OCI_COMMIT_ON_SUCCESS);

header("Location: pesanan.php");
?>
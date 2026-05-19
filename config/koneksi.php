<?php

$conn = oci_connect(
    'system',
    '111006',
    'localhost/FREEPDB1'
);

if (!$conn) {

    $e = oci_error();

    echo "Koneksi gagal";
    
} else {

    echo " ";

}

?>
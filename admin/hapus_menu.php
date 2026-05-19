<?php
include '../config/koneksi.php';

$id = $_GET['id'];

$query = oci_parse($conn,"
DELETE FROM menu_makanan
WHERE id = :id
");

oci_bind_by_name($query, ":id", $id);

$hapus = oci_execute($query, OCI_COMMIT_ON_SUCCESS);

if($hapus){

    echo "
    <script>
    alert('Menu berhasil dihapus!');
    window.location='menu.php';
    </script>
    ";

}else{

    echo "
    <script>
    alert('Gagal menghapus menu!');
    window.location='menu.php';
    </script>
    ";
}
?>
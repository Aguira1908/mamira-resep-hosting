<?php
include 'config/koneksi.php';

session_start();

$user = $_SESSION['user'];

$user_id = $user['ID'];

$nama = $_POST['nama'];

$wa = $_POST['wa'];

$alamat = $_POST['alamat'];

$pembayaran = $_POST['pembayaran'];

$total = $_POST['total'];

/* UPLOAD BUKTI */

$bukti = "";

if(isset($_FILES['bukti']) && $_FILES['bukti']['name'] != ""){

    $namaFile = $_FILES['bukti']['name'];

    $tmp = $_FILES['bukti']['tmp_name'];

    $bukti = time().'_'.$namaFile;

    move_uploaded_file(
        $tmp,
        "uploads/".$bukti
    );
}

/* INSERT PESANAN */

$query = oci_parse($conn,"

INSERT INTO pesanan
(
user_id,
total,
tanggal_pesan,
status,
nama,
wa,
alamat,
pembayaran,
bukti_pembayaran
)

VALUES
(
:user_id,
:total,
SYSDATE,
'Diproses',
:nama,
:wa,
:alamat,
:pembayaran,
:bukti
)

RETURNING id INTO :id

");

$id_pesanan = "";

oci_bind_by_name($query,":user_id",$user_id);

oci_bind_by_name($query,":total",$total);

oci_bind_by_name($query,":nama",$nama);

oci_bind_by_name($query,":wa",$wa);

oci_bind_by_name($query,":alamat",$alamat);

oci_bind_by_name($query,":pembayaran",$pembayaran);

oci_bind_by_name($query,":bukti",$bukti);

oci_bind_by_name($query,":id",$id_pesanan,32);

oci_execute($query, OCI_DEFAULT);

/* DETAIL PESANAN */

foreach($_SESSION['cart'] as $item){

    $menu_id = $item['id'];

    $qty = $item['qty'];

    $subtotal = $item['harga'] * $qty;

    $detail = oci_parse($conn,"

    INSERT INTO detail_pesanan
    (
    pesanan_id,
    menu_id,
    qty,
    subtotal
    )

    VALUES
    (
    :pesanan_id,
    :menu_id,
    :qty,
    :subtotal
    )

    ");

    oci_bind_by_name($detail,":pesanan_id",$id_pesanan);

    oci_bind_by_name($detail,":menu_id",$menu_id);

    oci_bind_by_name($detail,":qty",$qty);

    oci_bind_by_name($detail,":subtotal",$subtotal);

    oci_execute($detail, OCI_DEFAULT);

}

oci_commit($conn);

/* HAPUS CART */

unset($_SESSION['cart']);

echo "

<script>

alert('Pesanan berhasil dibuat!');

window.location='index.php';

</script>

";
?>
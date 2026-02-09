<?php
session_start();
include 'koneksi.php';
require_once 'log_helper.php';

$id    = $_POST['id_tarif'];
$tarif = $_POST['tarif_per_jam'];

query_log("
    UPDATE tb_tarif
    SET tarif_per_jam = '$tarif'
    WHERE id_tarif = '$id'
", "Mengubah tarif parkir dengan ID $id menjadi Rp $tarif / jam");

header("Location: index.php#tarif");
exit;
?>

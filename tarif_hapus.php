<?php
session_start();
include 'koneksi.php';
require_once 'log_helper.php';

$id = $_GET['id'];

query_log("
    DELETE FROM tb_tarif 
    WHERE id_tarif = '$id'
", "Menghapus tarif parkir dengan ID $id");

header("Location: index.php#tarif");
exit;
?>
    